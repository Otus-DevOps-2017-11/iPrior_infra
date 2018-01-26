resource "google_compute_firewall" "firewall_ssh" {
  name = "default-allow-ssh"
  network = "default"
  description = "Allow SSH from anywhere"

  source_ranges = "${var.source_ranges}"
  allow {
    protocol = "tcp"
    ports = ["22"]
  }
}
