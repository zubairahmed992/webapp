set :stage_dir, 'app/config/deploy' # needed for Symfony2 only
require 'capistrano/ext/multistage'
set :stages, %w(dev beta)

set :application, "lovethatfit"
set :app_path,    "app"

set :repository,  "git@github.com:lovethatfit/webapp.git"
set :scm,         :git
# Or: `accurev`, `bzr`, `cvs`, `darcs`, `subversion`, `mercurial`, `perforce`, or `none`

set :model_manager, "doctrine"
# Or: `propel`

set  :keep_releases,  3

# Be more verbose by uncommenting the following line
# logger.level = Logger::MAX_LEVEL

# custom settings
set :shared_files,      ["app/config/parameters.yml"]
set :shared_children,     [app_path + "/logs", web_path + "/uploads", "vendor"]
set :use_composer, true
set :update_vendors, true

set :use_sudo, false
set :user, "walkah"
set :writable_dirs,     ["app/cache", "app/logs"]
set :webserver_user,    "www-data"
set :permission_method, :acl
set :use_set_permissions, true

