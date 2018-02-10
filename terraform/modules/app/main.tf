resource "google_compute_instance" "app" {
  machine_type = "g1-small"
  name         = "${var.instance_name}"
  zone         = "${var.zone}"
  tags = ["${var.instance_name}"]

  "boot_disk" {
    initialize_params {
      image = "${var.app_disk_image}"
    }
  }

  "network_interface" {
    network       = "default"
    access_config = {
      nat_ip = "${google_compute_address.app_ip.address}"
    }
  }

  metadata {
    sshKeys = "appuser:${file(var.public_key_path)}"
  }
}


resource "google_compute_address" "app_ip" {
  name = "${var.instance_name}-ip"
}

resource "google_compute_firewall" "firewall_puma" {
  name    = "allow-puma-${var.instance_name}"
  network = "default"

  allow {
    protocol = "tcp"
    ports    = ["9292"]
  }

  source_ranges = ["0.0.0.0/0"]
  target_tags   = ["${var.instance_name}"]
}

resource "google_compute_firewall" "firewall_puma_http" {
  name    = "allow-puma-http-${var.instance_name}"
  network = "default"

  allow {
    protocol = "tcp"
    ports    = ["80"]
  }

  source_ranges = ["0.0.0.0/0"]
  target_tags   = ["${var.instance_name}"]
}
