<?php

namespace DreamFactory\Core\Oracle\Database\Schema;

use Illuminate\Database\Connection;
use Illuminate\Database\Schema\Grammars\Grammar;

class Comment extends Grammar
{
    /**
     * @var \Illuminate\Database\Connection
     */
    protected $connection;

    /**
     * @param Connection $connection
     */
    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * Set table and column comments.
     *
     * @param  OracleBlueprint $blueprint
     */
    public function setComments(OracleBlueprint $blueprint)
    {
        // Comment set by $table->comment = 'comment';
        $this->commentTable($blueprint);

        // Comments set by $table->string('column')->comment('comment');
        $this->fluentComments($blueprint);

        // Comments set by $table->commentColumns = ['column' => 'comment'];
        $this->commentColumns($blueprint);
    }

    /**
     * Run the comment on table statement.
     *
     * @param OracleBlueprint $blueprint
     */
    private function commentTable(OracleBlueprint $blueprint)
    {
        $table = $this->wrapValue($blueprint->getTable());

        if ($blueprint->comment != null) {
            $this->connection->statement("comment on table {$table} is '{$blueprint->comment}'");
        }
    }

    /**
     * Add comments set via fluent setter.
     *
     * @param OracleBlueprint $blueprint
     */
    private function fluentComments(OracleBlueprint $blueprint)
    {
        foreach ($blueprint->getColumns() as $column) {
            if (isset($column['comment'])) {
                $this->commentColumn($blueprint->getTable(), $column['name'], $column['comment']);
            }
        }
    }

    /**
     * Run the comment on column statement
     *
     * @param  string $table
     * @param  string $column
     * @param  string $comment
     */
    private function commentColumn($table, $column, $comment)
    {
        $table = $this->wrapValue($table);

        $column = $this->wrapValue($column);

        $this->connection->statement("comment on column {$table}.{$column} is '{$comment}'");
    }

    /**
     * Add comments on columns.
     *
     * @param OracleBlueprint $blueprint
     */
    private function commentColumns(OracleBlueprint $blueprint)
    {
        foreach ($blueprint->commentColumns as $column => $comment) {
            $this->commentColumn($blueprint->getTable(), $column, $comment);
        }
    }
}
