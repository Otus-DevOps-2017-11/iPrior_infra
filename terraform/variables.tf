variable "project" {
  description = "Project ID"
}

variable "region" {
  description = "Region"
  default     = "europe-west1"
}

variable "public_key_path" {
  description = "Path to the public key used for ssh access"
}

variable "private_key_path" {
  description = "Path to the private key used for ssh access"
}

variable "disk_image" {
  description = "Disk image"
}

variable "zone" {
  description = "Instance zone"
  default     = "europe-west1-b"
}

variable "project_ssh_keys" {
  description = "Project ssh keys: list user names. SSH key file must be exists by path ~/.ssh/{USERNAME}.pub"
  default = {}
}
