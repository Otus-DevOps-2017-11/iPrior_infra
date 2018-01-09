provider "google" {
  region  = "${var.region}"
  version = "1.4.0"
  project = "${var.project}"
}

resource "google_compute_instance" "app" {
  machine_type = "g1-small"
  name         = "reddit-app"
  zone         = "${var.zone}"

  "boot_disk" {
    initialize_params {
      image = "${var.disk_image}"
    }
  }

  "network_interface" {
    network       = "default"
    access_config = {}
  }

  tags = ["reddit-app"]

  metadata {
    sshKeys = "appuser:${file(var.public_key_path)}"
  }

  connection {
    type        = "ssh"
    user        = "appuser"
    agent       = false
    private_key = "${file(var.private_key_path)}"
  }

  provisioner "file" {
    source      = "files/puma.service"
    destination = "/tmp/puma.service"
  }

  provisioner "remote-exec" {
    script = "files/deploy.sh"
  }
}

resource "google_compute_firewall" "firewall_puma" {
  name    = "allow-puma-default"
  network = "default"

  allow {
    protocol = "tcp"
    ports    = ["9292"]
  }

  source_ranges = ["0.0.0.0/0"]
  target_tags   = ["reddit-app"]
}
