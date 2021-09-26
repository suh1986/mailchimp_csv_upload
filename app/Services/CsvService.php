<?php

namespace App\Services;

class CsvService
{

    protected $columns;

    protected $timestamp;

    protected $output_file_name;

    protected $data_file;

    protected $head_file;

    protected $has_data;

    /**************************************************************
     **
     ** Constructor will accept filename to be stored
     **
     **************************************************************/

    public function __construct($filename = '')
    {
        $this->output_file_name = $filename ?? $this->getTimestamp() . '.csv';
        $this->columns = [];
        $this->has_data = false;
    }

    /**************************************************************
     **
     ** Public methods
     **
     **************************************************************/

    /**
     * Create csv
     *
     * @param array $columns
     * @param array $data
     * @return string
     * @deprecated - we are no longer using this method
     */
    public function createCsv(array $columns, array $data, string $fileName = '')
    {
        $fileName = $fileName ?? time() . '.csv';
        $file_path = storage_path($fileName);

        $file = fopen($file_path, "w");

        fputcsv($file, $columns);

        foreach ($data as $row) {
            $temp = [];
            foreach ($columns as $header) {
                array_push($temp, ($row[$header] ?? ''));
            }
            fputcsv($file, $temp);
        }
        fclose($file);

        return $file_path;
    }

    public function collectData($data)
    {
        $csv_data = [];

        foreach ($this->columns as $colname) {
            array_push($csv_data, ($data[$colname] ?? ''));
            $this->has_data = true;
        }

        foreach ($data as $key => $value) {
            if (!in_array($key, $this->columns)) {
                array_push($this->columns, $key);
                array_push($csv_data, $value);
                $this->has_data = true;
            }
        }

        $this->writeCsv($csv_data, 'data');
    }

    public function finish()
    {
        if ($this->has_data) {
            $this->writeCsv($this->columns, 'head');
            return $this->merge();
        } else {
            $this->closeFiles();
            $this->cleanup();
            return null;
        }
    }

    public function hasData()
    {
        return $this->has_data;
    }

    public function fileExists($type)
    {
        return file_exists($this->getPath($type));
    }

    /**************************************************************
     **
     **  Private / Support Methods
     **
     **************************************************************/

    protected function getTimestamp()
    {
        if (!isset($this->timestamp)) {
            $this->timestamp = time();
        }
        return $this->timestamp;
    }

    protected function getPath($filename)
    {
        return storage_path($filename . '-' . $this->getTimestamp() . '.csv');
    }

    protected function getFile($filename, $mode = 'w')
    {
        if ($filename == 'data') {
            if (!isset($this->data_file)) {
                $path = $this->getPath('data');
                $this->data_file = fopen($path, $mode);
            }

            return $this->data_file;
        }

        if ($filename == 'head') {
            if (!isset($this->head_file)) {
                $path = $this->getPath('head');
                $this->head_file = fopen($path, $mode);
            }

            return $this->head_file;
        }
    }

    protected function writeCsv($arr, $type)
    {

        $file = $this->getFile($type);
        fputcsv($file, $arr, ',', '"');
    }

    protected function closeFiles()
    {
        if (isset($this->head_file)) {
            fclose($this->head_file);
        }
        if (isset($this->data_file)) {
            fclose($this->data_file);
        }
        unset($this->head_file);
        unset($this->data_file);
    }

    protected function cleanup()
    {
        foreach (['head', 'data'] as $type) {
            $this->unlinkFile($type);
        }
    }

    protected function unlinkFile($type)
    {
        if ($this->fileExists($type)) {
            unlink($this->getPath($type));
        }
    }

    protected function merge()
    {
        $this->closeFiles();

        $path = storage_path($this->output_file_name);
        $file = fopen($path, 'w');

        $hfile = $this->getFile('head', 'r+');
        while ($line = fgets($hfile)) {
            fputs($file, $line);
        }

        $dfile = $this->getFile('data', 'r+');
        while ($line = fgets($dfile)) {
            fputs($file, $line);
        }

        // Closing all files
        $this->closeFiles();
        fclose($file);

        $this->cleanup();

        return $path;
    }

    public function __destruct()
    {
        $this->closeFiles();
        $this->cleanup();
    }
}