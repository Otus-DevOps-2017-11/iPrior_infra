variable "public_key_path" {
  description = "Path to the public key used for ssh access"
}

variable "zone" {
  description = "Instance zone"
}

variable "db_disk_image" {
  description = "Disk image for reddit DB"
  default = "reddit-base-db"
}

variable "instance_name" {
  description = "Incatnce name"
  default = "reddit-db"
}

variable "app_instance_name" {
  description = "App incatnce name"
}
