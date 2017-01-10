lock '3.4.0'

set :application, 'autodeploy.io'
set :repo_url, 'git@github.com:sdieunidou/autodeploy.io.git'
set :linked_files, %w{app/config/parameters.yml}
set :linked_dirs, %w{app/logs vendor web/vendor node_modules web/assets}

set :format, :pretty
set :log_level, :debug
set :keep_releases, 5
