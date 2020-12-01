{ pkgs ? import <nixpkgs> {} }:

pkgs.mkShell {
  buildInputs = [
    (pkgs.php74.withExtensions ({ enabled, all }: enabled ++ [ all.xdebug ]))
    pkgs.php74Packages.composer
    pkgs.php74Packages.phpcs
    pkgs.php74Packages.phpstan
  ];

  COMPOSER_MEMORY_LIMIT=-1;
}
