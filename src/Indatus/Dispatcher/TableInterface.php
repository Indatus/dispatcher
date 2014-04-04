<?php namespace Indatus\Dispatcher;

/**
 * This file is part of Dispatcher
 *
 * (c) Ben Kuhl <bkuhl@indatus.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use cli\table\Renderer;

/**
 * Table class, only here so we can unit test our app
 */
interface TableInterface
{
    /**
     * Output the table to `STDOUT` using `cli\line()`.
     *
     * If STDOUT is a pipe or redirected to a file, should output simple
     * tab-separated text. Otherwise, renders table with ASCII table borders
     *
     * @uses cli\Shell::isPiped() Determine what format to output
     *
     * @todo I hate passionately that this writes directly to stdout... how do we get around that?
     *
     * @see cli\Table::renderRow()
     */
    public function display();

    /**
     * Set the footers of the table.
     *
     * @param array $footers An array of strings containing column footers names.
     */
    public function setFooters(array $footers);

    /**
     * Set the headers of the table.
     *
     * @param array $headers An array of strings containing column header names.
     */
    public function setHeaders(array $headers);

    /**
     * Add a row to the table.
     *
     * @param array $row The row data.
     * @see cli\Table::checkRow()
     */
    public function addRow(array $row);

    /**
     * Clears all previous rows and adds the given rows.
     *
     * @param array $rows A 2-dimensional array of row data.
     * @see cli\Table::addRow()
     */
    public function setRows(array $rows);

    public function resetTable();

    /**
     * Sets the renderer used by this table.
     *
     * @param \cli\table\Renderer $renderer The renderer to use for output.
     */
    public function setRenderer(Renderer $renderer);

    /**
     * Sort the table by a column. Must be called before `cli\Table::display()`.
     *
     * @param int $column The index of the column to sort by.
     */
    public function sort($column);
}
