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