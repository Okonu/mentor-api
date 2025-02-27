<?php

namespace App\Repositories\Mock;

use Illuminate\Support\Collection;

abstract class MockRepository
{
    protected Collection $data;
    protected static $nextId = 1;
    protected string $dataFile;

    public function __construct()
    {
        $this->data = collect([]);
        $this->loadInitialData();
    }

    /**
     * Load the initial mock data.
     */
    abstract protected function loadInitialData();

    /**
     * Get all records.
     */
    public function all()
    {
        return $this->data->values();
    }

    /**
     * Find a record by ID.
     */
    public function findById($id)
    {
        return $this->data->firstWhere('id', $id);
    }

    /**
     * Create a new record.
     */
    public function create(array $data)
    {
        $id = static::$nextId++;
        $record = array_merge(['id' => $id], $data);
        $this->data->put($id, $record);
        return $record;
    }

    /**
     * Update an existing record.
     */
    public function update($id, array $data)
    {
        if (!$this->data->has($id)) {
            return null;
        }

        $record = array_merge($this->data->get($id), $data);
        $this->data->put($id, $record);
        return $record;
    }

    /**
     * Delete a record.
     */
    public function delete($id)
    {
        if (!$this->data->has($id)) {
            return false;
        }

        $this->data->forget($id);
        return true;
    }

    /**
     * Helper to generate a unique ID for relations.
     */
    protected function generateUniqueId()
    {
        return uniqid();
    }
}
