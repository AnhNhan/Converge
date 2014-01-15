#!/usr/bin/env bash

wget https://apt.puppetlabs.com/puppetlabs-release-precise.deb
sudo dpkg -i puppetlabs-release-precise.deb

apt-get update

apt-get install puppet-common -y

puppet -V
