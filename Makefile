dev: install-dependencies jwt app-key rollback refresh seed-role seed-progress seed-jobtype seed-user seed-model
roll: rollback refresh seed-model seed-role seed-progress seed-jobtype seed-user seed-customer seed-vehicle seed-job seed-history
roll-main: rollback refresh seed-model seed-role seed-progress seed-jobtype seed-user
roll-list: seed-customer seed-model seed-vehicle seed-job seed-history

install-dependencies:
	composer update

jwt:
	php artisan jwt:secret

app-key:
	php artisan key:generate

rollback:
	php artisan migrate:rollback

refresh:
	php artisan migrate:refresh

seed-role:
	php artisan db:seed --class=RoleSeeder

seed-progress:
	php artisan db:seed --class=ProgressSeeder

seed-jobtype:
	php artisan db:seed --class=JobtypeSeeder

seed-model:
	php artisan db:seed --class=ModelSeeder

seed-user:
	php artisan db:seed --class=UserSeeder

seed-customer:
	php artisan db:seed --class=CustomerSeeder

seed-vehicle:
	php artisan db:seed --class=VehicleSeeder

seed-job:
	php artisan db:seed --class=JoblistSeeder

seed-history:
	php artisan db:seed --class=HistorySeeder