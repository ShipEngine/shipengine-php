with builtins;
{ pkgs ? import <nixpkgs> {} }:

pkgs.mkShell {
  buildInputs = [
    pkgs.php
    pkgs.php74Packages.composer
    pkgs.php74Packages.phpcs
    pkgs.php74Packages.phpstan
  ];
}
