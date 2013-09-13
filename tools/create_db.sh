psql -d template1 -c "CREATE USER test_haxe_pgsql_user WITH PASSWORD 'test_haxe_pgsql_pass';"
psql -d template1 -c "CREATE DATABASE test_haxe_pgsql;";
psql -d template1 -c "GRANT ALL PRIVILEGES ON DATABASE test_haxe_pgsql to test_haxe_pgsql_user;";
