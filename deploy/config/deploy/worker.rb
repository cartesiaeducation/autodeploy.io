set :stage, :prod
set :symfony_env, "prod"

set :branch, 'master'
set :deploy_to, '/home/autodeploy/www'

set :controllers_to_clear, ["app_*.php"]
set :composer_install_flags, '--prefer-dist --no-interaction --optimize-autoloader'

server 'worker1.autodeploy.io', user: 'autodeploy', port: 22, roles: %w{app}
server 'worker2.autodeploy.io', user: 'autodeploy', port: 22, roles: %w{app}

SSHKit.config.command_map[:composer] = "php #{shared_path.join("composer.phar")}"

after 'deploy:starting', 'composer:install_executable'
after 'deploy:updated', 'composer:dump_autoload'
after 'deploy:updated', 'doctrine:migrate'
