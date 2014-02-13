<?php
/**
 * @author Ben Kuhl <bkuhl@indatus.com>
 */

namespace Indatus\LaravelCommandScheduler;


interface Scheduleable {

    public function yearly();

    public function weekly();

    public function monthly();

    public function daily();

    public function hourly();

} 