variable "public_key_path" {
  description = "Path to the public key used for ssh access"
}

variable "zone" {
  description = "Instance zone"
}

variable "app_disk_image" {
  description = "Disk image for reddit app"
  default = "reddit-base-app"
}

variable "instance_name" {
  description = "Instance name"
  default = "reddit-app"
}
