provider "google" {
  region = "europe-west1"
  version = "1.4.0"
  project = "infra-189212"
}

resource "google_compute_instance" "app" {
  machine_type = "g1-small"
  name = "reddit-app"
  zone = "europe-west1-b"
  "boot_disk" {
    initialize_params {
      image = "reddit-base"
    }
  }
  "network_interface" {
    network = "default"
    access_config {}
  }
}