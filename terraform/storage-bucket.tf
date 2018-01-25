provider "google" {
  version = "1.4.0"
  project = "${var.project}"
  region = "${var.region}"
}
module "storage-bucket" {
  source = "SweetOps/storage-bucket/google"
  version = "0.1.1"
  name = ["otus-homework-09-01", "otus-homework-09-02"]
}
output storage-bucket_url {
  value = "${module.storage-bucket.url}"
}
