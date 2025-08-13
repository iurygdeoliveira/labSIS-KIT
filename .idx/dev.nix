# To learn more about how to use Nix to configure your environment
# see: https://developers.google.com/idx/guides/customize-idx-env
{pkgs}: {
  # Which nixpkgs channel to use.
  channel = "stable-24.05"; # or "unstable"
  # Use https://search.nixos.org/packages to find packages
  packages = [
    pkgs.php83
    pkgs.php83Packages.composer
    pkgs.nodejs_22
    pkgs.busybox
    pkgs.sudo
    pkgs.openssh
    pkgs.nano
  ];
  # Sets environment variables in the workspace
  env = {};
  idx = {
    # Search for the extensions you want on https://open-vsx.org/ and use "publisher.id"
    extensions = [
      # "vscodevim.vim"
    ];
    workspace = {
      # Runs when a workspace is first created with this `dev.nix` file
      onCreate = {
        # Example: install JS dependencies from NPM
        setup-laravel = "cp .env.example .env && composer update && touch database/database.sqlite && php artisan migrate:fresh --seed && php artisan key:generate && php artisan dependency:versions && ./reset.sh";
        # Open editors for the following files by default, if they exist:
        default.openFiles = [ "resources/views/welcome.blade.php" ];
      };
      # To run something each time the workspace is (re)started, use the `onStart` hook
    };
    # Enable previews and customize configuration
    previews = {
      enable = true;
      previews = {
        web = {
          command = ["php" "artisan" "serve" "--port" "$PORT" "--host" "0.0.0.0"];
          manager = "web";
        };
      };
    };
  };
}
