set :stage, :prod
set :symfony_env, "prod"

set :branch, 'master'
set :deploy_to, '/home/autodeploy/www'

set :controllers_to_clear, ["app_*.php"]
set :composer_install_flags, '--prefer-dist --no-interaction --optimize-autoloader'

server '5.39.62.247', user: 'autodeploy', port: 22, roles: %w{app db web}
SSHKit.config.command_map[:composer] = "php #{shared_path.join("composer.phar")}"

after 'deploy:starting', 'composer:install_executable'
after 'deploy:updated', 'composer:dump_autoload'
after 'deploy:updated', 'doctrine:migrate'
after 'deploy:updated', 'npm:install'
after 'deploy:updated', 'bower:install'
after 'deploy:updated', 'symfony:assets:install'
after 'deploy:updated', 'symfony:assetic:dump'
after 'deploy:updated', 'php5:reload'
after 'deploy:updated', 'translations:import'
