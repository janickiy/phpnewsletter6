<?php

namespace App\Helper;

/*
 * Helper functions for building a DataTables server-side processing SQL query
 *
 * The static functions in this class are just helper functions to help build
 * the SQL used in the DataTables demo server-side processing scripts. These
 * functions obviously do not represent all that can be done with server-side
 * processing, they are intentionally simple to show how it works. More complex
 * server-side processing operations will likely require a custom script.
 *
 * See http://datatables.net/usage/server-side for full details on the server-
 * side processing requirements of DataTables.
 *
 * @license MIT - http://datatables.net/license_mit
 * @editedby (SilverX) Chris R. McLeod
 *
 */

use PDO;

class Ssp {
    /**
     * Create the data output array for the DataTables rows
     *
     *  @param  array $columns Column information array
     *  @param  array $data    Data from the SQL get
     *  @return array          Formatted data in a row based format
     */
    private static function data_output ( $options, $data ) {
        $out = array();
        $columns = $options['columns'];
        for ( $i=0, $ien=count($data) ; $i<$ien ; $i++ ) {
            $row = array();
            for ( $j=0, $jen=count($columns) ; $j<$jen ; $j++ ) {
                $column = $columns[$j];
                // Is there a formatter?
                if ( isset( $column['formatter'] ) ) {
                    $row[ $column['dt'] ] = $column['formatter']( $data[$i][ self::column_name_out($column) ], $data[$i] );
                }
                else {
                    $row[ $column['dt'] ] = $data[$i][ self::column_name_out($column) ];
                }
            }
            $out[] = $row;
        }
        return $out;
    }

    /**
     * Perform the SQL queries needed for an server-side processing requested,
     * utilising the helper functions of this class, limit(), order() and
     * filter() among others. The returned array is ready to be encoded as JSON
     * in response to an SSP request, or can be modified if needed before
     * sending back to the client.
     *
     *  @param  array $request Data sent to server by DataTables
     *  @param  array|PDO $conn PDO connection resource or connection parameters array
     *  @param  string $table SQL table to query
     *  @param  string $primaryKey Primary key of the table
     *  @param  array $columns Column information array
     *  @return array          Server-side processing response array
     */
    static function process ( $request, $conn, $options ) {
        $bindings = array();
        $db = self::db( $conn );

        if (!isset($options['alias']))
            $options['alias'] = $options['table'][0];

        // Build the SQL query string from the request
        $limitSql = self::limit( $request );
        $orderSql = self::order( $request, $options );
        $whereSql = self::filter( $request, $options, $bindings );
        $joinSql  = self::table_join( $options );

        $whereAllSql = '';

        if (isset($options['whereResult'])) {
            $optWhere = self::where_options( $options['whereResult'], $options['alias'], $bindings );

            $whereSql = $whereSql ?
                $whereSql .' AND '.$optWhere :
                'WHERE '.$optWhere;
        }

        if (isset($options['where'])) {
            $optWhere = self::where_options( $options['where'], $options['alias'], $bindings );

            $whereSql = $whereSql ?
                $whereSql .' AND '.$optWhere :
                'WHERE '.$optWhere;

            $whereAllSql = 'WHERE '.$optWhere;
        }

        $query = "SELECT ".implode(", ", self::column_names( $options ))." 
			FROM `{$options['table']}` {$options['alias']}
			$joinSql
			$whereSql
			$orderSql
			$limitSql";

        // Main query to actually get the data
        $data = self::sql_exec( $db, $bindings, $query);

        // Data set length after filtering
        $resFilterLength = self::sql_exec( $db, $bindings,
            "SELECT COUNT({$options['alias']}.`{$options['primaryKey']}`)
			 FROM `{$options['table']}` {$options['alias']}
			 $joinSql
			 $whereSql"
        );
        $recordsFiltered = $resFilterLength[0][0];

        // Total data set length
        $resTotalLength = self::sql_exec( $db, $bindings,
            "SELECT COUNT({$options['alias']}.`{$options['primaryKey']}`)
			 FROM `{$options['table']}` {$options['alias']}
			 $joinSql
			 $whereAllSql"
        );
        $recordsTotal = $resTotalLength[0][0];

        /*
         * Output
         */
        return array(
            "draw"            => isset ( $request['draw'] ) ? intval( $request['draw'] ) : 0,
            "recordsTotal"    => intval( $recordsTotal ),
            "recordsFiltered" => intval( $recordsFiltered ),
            "data"            => self::data_output( $options, $data )
        );
    }

    /**
     * Database connection
     *
     * Obtain an PHP PDO connection from a connection details array
     *
     *  @param  array $conn SQL connection details. The array should have
     *    the following properties
     *     * host - host name
     *     * db   - database name
     *     * user - user name
     *     * pass - user password
     *  @return resource PDO connection
     */
    private static function db ( $conn ) {
        if ( is_array( $conn ) ) {
            return self::sql_connect( $conn );
        }

        return $conn;
    }


    /**
     * Paging
     *
     * Construct the LIMIT clause for server-side processing SQL query
     *
     *  @param  array $request Data sent to server by DataTables
     *  @param  array $columns Column information array
     *  @return string SQL limit clause
     */
    private static function limit ( $request ) {
        $limit = '';

        if ( isset($request['start']) && $request['length'] != -1 ) {
            $limit = "LIMIT ".intval($request['start']).", ".intval($request['length']);
        }

        return $limit;
    }


    /**
     * Ordering
     *
     * Construct the ORDER BY clause for server-side processing SQL query
     *
     *  @param  array $request Data sent to server by DataTables
     *  @param  array $columns Column information array
     *  @return string SQL order by clause
     */
    private static function order ( $request, $options ) {
        $order = '';
        $columns = $options['columns'];

        if ( isset($request['order']) && count($request['order']) ) {
            $orderBy = array();
            $dtColumns = self::pluck( $columns, 'dt' );

            for ( $i=0, $ien=count($request['order']) ; $i<$ien ; $i++ ) {
                // Convert the column index into the column data property
                $columnIdx = intval($request['order'][$i]['column']);
                $requestColumn = $request['columns'][$columnIdx];

                $columnIdx = array_search( $requestColumn['data'], $dtColumns );
                $column = $columns[ $columnIdx ];

                if ( $requestColumn['orderable'] == 'true' ) {
                    $dir = $request['order'][$i]['dir'] === 'asc' ?
                        'ASC' :
                        'DESC';

                    $orderBy[] = self::column_name_ref($column, $options['alias']).' '.$dir;
                }
            }

            $order = 'ORDER BY '.implode(', ', $orderBy);
        }

        return $order;
    }


    /**
     * Searching / Filtering
     *
     * Construct the WHERE clause for server-side processing SQL query.
     *
     * NOTE this does not match the built-in DataTables filtering which does it
     * word by word on any field. It's possible to do here performance on large
     * databases would be very poor
     *
     *  @param  array $request Data sent to server by DataTables
     *  @param  array $columns Column information array
     *  @param  array $bindings Array of values for PDO bindings, used in the
     *    sql_exec() function
     *  @return string SQL where clause
     */
    private static function filter ( $request, $options, &$bindings ) {
        $globalSearch = array();
        $columnSearch = array();

        $columns = $options['columns'];
        $dtColumns = self::pluck( $columns, 'dt' );

        if ( isset($request['search']) && $request['search']['value'] != '' ) {
            $str = $request['search']['value'];

            for ( $i=0, $ien=count($request['columns']) ; $i<$ien ; $i++ ) {
                $requestColumn = $request['columns'][$i];
                $columnIdx = array_search( $requestColumn['data'], $dtColumns );
                $column = $columns[ $columnIdx ];

                if ( $requestColumn['searchable'] == 'true' ) {
                    $binding = self::bind( $bindings, '%'.$str.'%', PDO::PARAM_STR );
                    $globalSearch[] = self::column_name_ref($column, $options['alias'])." LIKE ".$binding;
                }
            }
        }

        // Individual column filtering
        if ( isset( $request['columns'] ) ) {
            for ( $i=0, $ien=count($request['columns']) ; $i<$ien ; $i++ ) {
                $requestColumn = $request['columns'][$i];
                $columnIdx = array_search( $requestColumn['data'], $dtColumns );
                $column = $columns[ $columnIdx ];

                $str = $requestColumn['search']['value'];

                if ( $requestColumn['searchable'] == 'true' &&
                    $str != '' ) {
                    $binding = self::bind( $bindings, '%'.$str.'%', PDO::PARAM_STR );
                    $columnSearch[] = self::column_name_ref($column, $options['alias'])." LIKE ".$binding;
                }
            }
        }

        // Combine the filters into a single string
        $where = '';

        if ( count( $globalSearch ) ) {
            $where = '('.implode(' OR ', $globalSearch).')';
        }

        if ( count( $columnSearch ) ) {
            $where = $where === '' ?
                implode(' AND ', $columnSearch) :
                $where .' AND '. implode(' AND ', $columnSearch);
        }

        if ( $where !== '' ) {
            $where = 'WHERE '.$where;
        }

        return $where;
    }

    /**
     * Retrieves additional WHERE parameters from the supplied DataTable options.
     * Used to globally filter results by fields not supplied by a DataTable request.
     *	@param  array  $where_opts  The options to build the clause from. Two types exist, "where" and "whereResult".
     *	@param  string $tableAlias  The alias of the table in 'FROM' statement.
     *	@return string              WHERE clause SQL to use in select query.
     */
    private static function where_options ( $where_opts, $tableAlias, &$bindings ) {
        $wheres = [];
        foreach($where_opts as $i => $where) {
            $alias = isset($where['alias']) ? $where['alias'] : $tableAlias;
            $binding = self::bind($bindings, $where['value']);

            $wheres[] = "$alias.`{$where['db']}` {$where['op']} $binding";
        }

        return implode(', AND ', $wheres);
    }

    /**
     * Retrieves a column name as it would appear in a select statement.
     * This includes any "as" parameters applied to the column.
     *	@param  array  $options     The column to retrieve information from.
     *	@param  string $tableAlias  The alias of the table in 'FROM' statement.
     *	@return string              JOIN statement(s) SQL to use in select query.
     */
    private static function table_join( $options ) {
        $joins = [];
        $columns = $options['columns'];
        for ($i = 0; $i < count($columns); $i++) {
            $col = $columns[$i];
            if (isset($col['join'])) {
                $join = $col['join'];
                $table = $join['table'];
                $alias = isset($join['alias']) ? $join['alias'] : $table[0];
                $joins[$alias] = "JOIN `$table` $alias ON ($alias.`{$join['on']}` = {$options['alias']}.`{$col['db']}`)";
            }
        }
        return implode(' ', $joins);
    }

    /**
     * Retrieves a list of column names as they would appear in a select statement.
     * This includes any "as" parameters applied to the column.
     *	@param  array $options Array of DataTable options to retrieve information from.
     *	@return array          List of column name strings to use in SQL select query.
     */
    private static function column_names ( $options ) {
        $names = [];
        $columns = $options['columns'];
        for ($i = 0; $i < count($columns); $i++) {
            $names[] = self::column_name($columns[$i], $options['alias']);
        }
        return $names;
    }

    /**
     * Retrieves a column name as it would appear in a select statement.
     * This includes any "as" parameters applied to the column.
     *	@param  array  $column      The column to retrieve information from.
     *	@param  string $tableAlias  The alias of the table in 'FROM' statement.
     *	@return string              Column name string to use in SQL select query.
     */
    private static function column_name ( $column, $tableAlias ) {
        if (isset($column['join'])) {
            $join = $column['join'];
            $join['alias'] = isset($join['alias']) ? $join['alias'] : $join['table'][0];
            return "{$join['alias']}.`{$join['select']}`".(isset($join['as']) ? " AS '{$join['as']}'" : '');
        }
        return "$tableAlias.`{$column['db']}`".(isset($column['as']) ? " AS '{$column['as']}'" : '');
    }

    /**
     * Retrieves a column name as it would appear in a clause (WHERE, GROUP BY, ORDER BY).
     * This is typically the column's original name unless joined with an alias or using "as".
     *	@param  array  $column      The column to retrieve information from.
     *	@param  string $tableAlias  The alias of the table in 'FROM' statement.
     *	@return string              Column name string to use in SQL clause.
     */
    private static function column_name_ref ( $column, $tableAlias ) {
        if (isset($column['join'])) {
            $join = $column['join'];
            $join['alias'] = isset($join['alias']) ? $join['alias'] : $join['table'][0];
            return isset($join['as']) ? "`{$join['as']}`" : "{$join['alias']}.`{$join['select']}`";
        }
        return isset($column['as']) ? "`{$column['as']}`" : "$tableAlias.`{$column['db']}`";
    }

    /**
     * Retrieves a column name as it would appear in the final resultset.
     * Results have the alias' removed and will refer to it's "as", if any.
     *	@param  array  $column  The column to retrieve information from.
     *	@return string          Column name string that will appear in the results.
     */
    private static function column_name_out ( $column ) {
        if (isset($column['join'])) {
            $join = $column['join'];
            return isset($join['as']) ? $join['as'] : $join['select'];
        }
        return isset($column['as']) ? $column['as'] : $column['db'];
    }

    /**
     * Connect to the database
     *
     * @param  array $sql_details SQL server connection details array, with the
     *   properties:
     *     * host - host name
     *     * db   - database name
     *     * user - user name
     *     * pass - user password
     * @return resource Database connection handle
     */
    private static function sql_connect ( $sql_details )
    {
        try {
            $db = @new PDO(
                "mysql:host={$sql_details['host']};dbname={$sql_details['db']}",
                $sql_details['user'],
                $sql_details['pass'],
                array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'" ?? $sql_details['charset'])
            );
        }
        catch (PDOException $e) {
            self::fatal(
                "An error occurred while connecting to the database. ".
                "The error reported by the server was: ".$e->getMessage()
            );
        }

        return $db;
    }


    /**
     * Execute an SQL query on the database
     *
     * @param  resource $db  Database handler
     * @param  array    $bindings Array of PDO binding values from bind() to be
     *   used for safely escaping strings. Note that this can be given as the
     *   SQL query string if no bindings are required.
     * @param  string   $sql SQL query to execute.
     * @return array         Result from the query (all rows)
     */
    private static function sql_exec ( $db, $bindings, $sql=null )
    {
        // Argument shifting
        if ( $sql === null ) {
            $sql = $bindings;
        }

        $stmt = $db->prepare( $sql );
        //echo $sql;

        // Bind parameters
        if ( is_array( $bindings ) ) {
            for ( $i=0, $ien=count($bindings) ; $i<$ien ; $i++ ) {
                $binding = $bindings[$i];
                $stmt->bindValue( $binding['key'], $binding['val'], $binding['type'] );
            }
        }

        // Execute
        try {
            $stmt->execute();
        }
        catch (PDOException $e) {
            self::fatal( "An SQL error occurred: ".$e->getMessage() );
        }

        // Return all
        return $stmt->fetchAll( PDO::FETCH_BOTH );
    }


    /* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
     * Internal methods
     */

    /**
     * Throw a fatal error.
     *
     * This writes out an error message in a JSON string which DataTables will
     * see and show to the user in the browser.
     *
     * @param  string $msg Message to send to the client
     */
    private static function fatal ( $msg )
    {
        echo json_encode( array(
            "error" => $msg
        ) );

        exit(0);
    }

    /**
     * Create a PDO binding key which can be used for escaping variables safely
     * when executing a query with sql_exec()
     *
     * @param  array &$a    Array of bindings
     * @param  *      $val  Value to bind
     * @param  int    $type PDO field type
     * @return string       Bound key to be used in the SQL where this parameter
     *   would be used.
     */
    private static function bind ( &$a, $val, $type = NULL )
    {
        $key = ':binding_'.count( $a );

        $a[] = array(
            'key' => $key,
            'val' => $val,
            'type' => isset($type) ? $type : (is_numeric($val) ? PDO::PARAM_INT : PDO::PARAM_STR)
        );

        return $key;
    }


    /**
     * Pull a particular property from each assoc. array in a numeric array,
     * returning and array of the property values from each item.
     *
     *  @param  array  $a    Array to get data from
     *  @param  string $prop Property to read
     *  @return array        Array of property values
     */
    private static function pluck ( $a, $prop )
    {
        $out = array();

        for ( $i=0, $len=count($a) ; $i<$len ; $i++ ) {
            $out[] = $a[$i][$prop];
        }

        return $out;
    }


    /**
     * Return a string from an array or a string
     *
     * @param  array|string $a Array to join
     * @param  string $join Glue for the concatenation
     * @return string Joined string
     */
    private static function _flatten ( $a, $join = ' AND ' )
    {
        if ( ! $a ) {
            return '';
        }
        else if ( $a && is_array($a) ) {
            return implode( $join, $a );
        }
        return $a;
    }
}
