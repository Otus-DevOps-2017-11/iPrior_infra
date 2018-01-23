# iPrior_infra

## Homework 05

### Исследовать способ подключения к internalhost в одну команду

Для автоматического запуска ssh агента при входе в систему, а так же для автоматического добавления необходимых ключей в агента, нужно проделать следующее:

*  В файд ~/.profile добавить следющий код:

```bash
#
# setup ssh-agent
#


SSH_ENV="$HOME/.ssh/environment"

function start_agent {
     echo "Initialising new SSH agent..."
     /usr/bin/ssh-agent | sed 's/^echo/#echo/' > "${SSH_ENV}"
     echo succeeded
     chmod 600 "${SSH_ENV}"
     . "${SSH_ENV}" > /dev/null
     /usr/bin/ssh-add;
}

# Source SSH settings, if applicable

if [ -f "${SSH_ENV}" ]; then
     . "${SSH_ENV}" > /dev/null
     #ps ${SSH_AGENT_PID} doesn't work under cywgin
     ps -ef | grep ${SSH_AGENT_PID} | grep ssh-agent$ > /dev/null || {
         start_agent;
     }
else
     start_agent;
fi

# 
# added keys
#

aah-add -L
ssh-add ~/.ssh/appuser # указываем путь до публичного ssh ключа 

echo "SSH public keys added"
```

* Для подключение к хосту someinternalhost через хост bastion использовать команду:

```bash
ssh -i ~/.ssh/appuser -A -t appuser@<ВНЕШНИЙ_IP_АДРЕС_BASTION> ssh -A <ВНУТРЕННИЙ_IP_АДРЕС_SOMEINTERNALHOST>
```

**Используемая "литература"**:

* [Using ssh-agent with ssh](http://mah.everybody.org/docs/ssh)
* [Transparent Multi-hop SSH](http://sshmenu.sourceforge.net/articles/transparent-mulithop.html)

### Доп. задание - подключения из консоли при помощи команды вида ssh internalhost

В файл ~/.ssh/config добавим следующие строки:

**файл возможно придется создать, если его не существует)**

```bash
Host bastion
    HostName <ВНЕШНИЙ_IP_АДРЕС_BASTION>
    User appuser
    CertificateFile ~/.ssh/appuser

Host internalhost
    HostName <ВНУТРЕННИЙ_IP_АДРЕС_SOMEINTERNALHOST>
    User appuser
    CertificateFile ~/.ssh/appuser
    ProxyCommand ssh -q bastion nc -q0 internalhost 22
```

Теперь для подключения к хосту bastion достаточно вполнить команду:

```bash
ssh bastion
```

Для подключения к хосту someinternalhost через хост bastion достаточно выполнить команду:

```bash
ssh internalhost
```

**Используемая "литература"**:

* [Ubuntu manuals - ssh_config](http://manpages.ubuntu.com/manpages/zesty/en/man5/ssh_config.5.html)
* [Transparent Multi-hop SSH](http://sshmenu.sourceforge.net/articles/transparent-mulithop.html)


#### Сокращаем вызов команды 

Так же возможно полностью сократить команду для подключения к хосту someinternalhost до одного слова.

В файл ~/.bashrc в конец добавим алиас:

```bash
alias internalhost='ssh -i ~/.ssh/appuser -A -t appuser@<ВНЕШНИЙ_IP_АДРЕС_BASTION> ssh -A <ВНУТРЕННИЙ_IP_АДРЕС_SOMEINTERNALHOST>'
```

Теперь, для подключения к хосту someinternalhost достаточно ввести команду:

```bash
internalhost
```

### получившуюся конфигурацию и данные для подключения

| Host             | IP ext       | IP int     |
|------------------|--------------|------------|
| bastion          | 35.205.86.29 | 10.132.0.2 |
| someinternalhost |              | 10.132.0.3 |




## Homework 06

Используемая команда GCloud для создания instance:

```bash
gcloud compute instances create reddit-app\
 --boot-disk-size=10GB \
 --image-family ubuntu-1604-lts \
 --image-project=ubuntu-os-cloud \
 --machine-type=g1-small \
 --tags puma-server \
 --restart-on-failure \
 --zone=europe-west3-a \
 --metadata startup-script='#!/bin/bash
cd /tmp && wget https://raw.githubusercontent.com/Otus-DevOps-2017-11/iPrior_infra/Infra-2/startup_script.sh
sudo chmod 0755 /tmp/startup_script.sh
/tmp/startup_script.sh
'
```

**или, как изначальный вариант**:

```bash
gcloud compute instances create reddit-app\
 --boot-disk-size=10GB \
 --image-family ubuntu-1604-lts \
 --image-project=ubuntu-os-cloud \
 --machine-type=g1-small \
 --tags puma-server \
 --restart-on-failure \
 --zone=europe-west3-a \
 --metadata startup-script='#!/bin/bash
sudo apt-get update
sudo apt-get install -y git-core
git clone https://github.com/Otus-DevOps-2017-11/iPrior_infra.git /tmp/otus
cd /tmp/otus && git checkout Infra-2
sudo chmod 0755 /tmp/otus/install_ruby.sh
sudo chmod 0755 /tmp/otus/install_mongodb.sh
sudo chmod 0755 /tmp/otus/deploy.sh
/tmp/otus/install_ruby.sh > /tmp/install_ruby.log
/tmp/otus/install_mongodb.sh > /tmp/install_mongo.log
sudo su - appuser -c "/tmp/otus/deploy.sh > /tmp/deploy.log"
'
```


## Homework 07

Для создания базового образа (без приложения):

```bash
packer build -var-file=variables.json ./ubuntu16.json
```

Для создания образа с приложением:

```bash
packer build -var-file=variables.json ./immutable.json
```

Для создания интанса с запущенным приложением:

```bash
./create-reddit-vm.sh
```


## Homework 08

### One Star

**Задание**:

> * Опишите в коде терраформа добавление ssh ключа пользователя appuser1 в метаданные проекта. Выполните terraform apply и проверьте результат (публичный ключ можно брать пользователя appuser); 
> * Опишите в коде терраформа добавление ssh ключей нескольких пользователей в метаданные проекта (можно просто один и тот же публичный ключ, но с разными именами пользователей, например appuser1, appuser2 и т.д.). Выполните terraform apply и проверьте результат;

* в файл *main.tf* добавил ресурс `"google_compute_project_metadata_item" "ssh_keys"`. `value` данного ресурса генерится с использованием функции `join` и рендеринга шаблона `"template_file" "ssh_keys_templ"` _(см.ниже)_
* в файл *main.tf* добавил `"template_file" "ssh_keys_templ"` для генерации `value` для ресурса `"google_compute_project_metadata_item" "ssh_keys"`. Значения берутся из `project_ssh_keys` _(см.ниже)_
* в файл *variables.tf* добавил переменную `project_ssh_keys` - список.
Предполагается в данную переменную писать имена пользователей для добавления ssh ключей в проект, **при условии**, что файл с SSH ключем находится по пути: **~/.ssh/{USERNAME}.pub**


Для выполнения первого пункта задания, а именно:

> Опишите в коде терраформа добавление ssh ключа пользователя appuser1 в метаданные проекта.

необходимо в файле *terraform.tfvars* добавить следующие строки:
```text
project_ssh_keys = {
  "0" = "appuser1"
}
```

Для выполнения второго пункта задачи, а именно:
> Опишите в коде терраформа добавление ssh ключей нескольких пользователей в метаданные проекта

необходимо в файле *terraform.tfvars* дополнить список пользователей в в значении переменной `project_ssh_keys`, например:
```text
project_ssh_keys = {
  "0" = "appuser1"
  "1" = "appuser2"
  "2" = "appuser3"
}
```

**P.S.**
_наверное можно было бы не привязывать имя пользователя к имени файла (пути до файла), а сделать через map... но я что-то уже засыпаю, попробую потом переделать. Может успею до approve -a_

**Задание**:

> * Добавьте в веб интерфейсе ssh ключ пользователю appuser_web в метаданные проекта. Выполните terraform apply и проверьте результат; 
> * Какие проблемы вы обнаружили?

После выполнения `terraform apply` ssh ключ пользователя *appuser_web* пропал, что не удивительно, так как terraform приводит "параметры" к своему "идеальному миру".

Является ли это проблемой? - наверное да, при многопользовательском режиме использования terraform.


## Homework 09

Сделал два окружения, *prod* и *stage*

Оба окружения используют модули *vpc*, *app* и *db*

В модули *app* и *db* добавил параметр *instance_name* - он участвует в генерации имени инстанца, тегов и т.д.

В модуле *db* добавил дополнительный параметр, *app_instance_name*, который используется для создания правила firewall -a _(что бы приложение имело доступ к базе данных)_


## Homework 10

* Создан файл `ansible.cfg` с настройками ansible
* Создан файл `inventory` с двумя хостами (app & db) а так же вариации этого файла в других форматах:
 * `inventory.yml`
 * `inventory.json` _(с динамической инвентаризацией не разобрался, см.ниже)_
 
С командами "поигрался" по разному...

не знаю что еще написать.

### One Star - inventory.json 

Не разобрался с Dynamic Inventory

Нашел такое описание: http://docs.ansible.com/ansible/latest/guide_gce.html#

А так же gce.py вот тут: https://github.com/ansible/ansible/tree/devel/contrib/inventory

Но запустить не удалось, питон падает с ошибками =|


