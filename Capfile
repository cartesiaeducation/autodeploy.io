set :deploy_config_path, "deploy/config/deploy.rb"
set :stage_config_path, "deploy/config/deploy/"

require 'capistrano/setup'
require 'capistrano/deploy'

require 'capistrano/composer'
require 'capistrano/symfony'
require 'capistrano/npm'
require 'capistrano/bower'

Dir.glob('deploy/tasks/*.cap').each { |r| import r }
