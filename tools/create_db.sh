psql -d template1 -c "CREATE USER test_haxe_user WITH PASSWORD 'test_haxe_pass';"
psql -d template1 -c "CREATE SCHEMA test_haxe_schema AUTHORIZATION test_haxe_user;";
psql -d template1 -c "CREATE DATABASE test_haxe_db;";
psql -d template1 -c "ALTER DATABASE test_haxe_db SET search_path TO test_haxe_schema;";
psql -d template1 -c "GRANT USAGE ON SCHEMA test_haxe_schema to test_haxe_user;";
psql -d template1 -c "GRANT ALL PRIVILEGES ON DATABASE test_haxe_db to test_haxe_user;";
