resource "google_compute_instance" "db" {
  machine_type = "g1-small"
  name         = "${var.instance_name}"
  zone         = "${var.zone}"
  tags = ["${var.instance_name}"]

  "boot_disk" {
    initialize_params {
      image = "${var.db_disk_image}"
    }
  }

  "network_interface" {
    network       = "default"
    access_config = {}
  }

  metadata {
    sshKeys = "appuser:${file(var.public_key_path)}"
  }
}

resource "google_compute_firewall" "firewall_mongo" {
  name    = "allow-mongo-${var.instance_name}"
  network = "default"

  allow {
    protocol = "tcp"
    ports    = ["27017"]
  }
  target_tags   = ["${var.instance_name}"]
  source_tags   = ["${var.app_instance_name}"]
}
