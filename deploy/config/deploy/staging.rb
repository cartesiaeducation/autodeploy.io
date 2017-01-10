set :stage, :staging
set :symfony_env, "staging"

set :branch, 'develop'
set :deploy_to, '/home/autodeploy/www'

set :controllers_to_clear, []
set :composer_install_flags, '--prefer-dist --no-interaction --optimize-autoloader'

server 'staging.autodeploy.io', user: 'autodeploy', port: 22, roles: %w{app db web}
SSHKit.config.command_map[:composer] = "php #{shared_path.join("composer.phar")}"

after 'deploy:starting', 'composer:install_executable'
after 'deploy:updated', 'composer:dump_autoload'
after 'deploy:updated', 'doctrine:migrate'
after 'deploy:updated', 'npm:install'
after 'deploy:updated', 'bower:install'
after 'deploy:updated', 'symfony:assets:install'
after 'deploy:updated', 'symfony:assetic:dump'
after 'deploy:updated', 'php5:reload'
