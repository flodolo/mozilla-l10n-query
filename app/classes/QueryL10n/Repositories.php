<?php
namespace QueryL10n;

use Json\Json;

/**
 * Repositories class
 *
 * This class is used to extract all the data we need about repositories
 *
 * @package QueryL10n
 */
class Repositories
{
    /**
     * @var array $repo_list  List of supported repositories
     */
    private $repo_list;

    /**
     * @var string $source_path  Path for sources
     */
    private $source_path;

    /**
     * The constructor sets shared variables
     *
     * @param string $path Path to folder with source files
     */
    public function __construct($path)
    {
        $this->source_path = $path;
        $this->repo_list = $this->getRepositoriesJson();
    }

    /**
     * Return array with data for all repositories
     *
     * @return array Data repositories
     */
    public function getRepositoriesJson()
    {
        $json_data = new Json($this->source_path . 'repositories.json');

        return $json_data->fetchContent();
    }

    /**
     * Return a full list of repositories with ID, pretty name and
     * supported locales
     *
     * @return array List of supported repositories
     */
    public function getRepositories()
    {
        $result = [];
        foreach ($this->repo_list as $repo) {
            $result[] = [
                'id'            => $repo['id'],
                'display_order' => $repo['display_order'],
                'name'          => $repo['name'],
                'locales'       => $this->getSupportedLocales($repo['id']),
                'type'          => $repo['type'],
            ];
        }

        return $result;
    }

    /**
     * Return list of supported repositories with ID, pretty name and
     * supported locales
     *
     * @param string $type Type of repository
     *
     * @return array List of supported repositories
     */
    public function getTypeRepositories($type)
    {
        $result = [];
        foreach ($this->repo_list as $repo) {
            if ($repo['type'] == $type) {
                $result[] = [
                    'id'            => $repo['id'],
                    'display_order' => $repo['display_order'],
                    'name'          => $repo['name'],
                    'locales'       => $this->getSupportedLocales($repo['id']),
                    'type'          => $repo['type'],
                ];
            }
        }

        return $result;
    }

    /**
     * Return data about a single repository
     *
     * @param string $repo_id Repository ID
     *
     * @return mixed Data about the requested repository,
     *               false if unknown repo
     */
    public function getSingleRepository($repo_id)
    {
        if (isset($this->repo_list[$repo_id])) {
            $repo = $this->repo_list[$repo_id];

            return [
                'id'            => $repo['id'],
                'name'          => $repo['name'],
                'display_order' => $repo['display_order'],
                'locales'       => $this->getSupportedLocales($repo['id']),
                'type'          => $repo['type'],
            ];
        }

        return false;
    }

    /**
     * Return a list of locales supported for a specific repo ID
     *
     * @param string $repo_id Repository ID
     *
     * @return array List of supported locales
     */
    public function getSupportedLocales($repo_id)
    {
        $locales = [];
        if (isset($this->repo_list[$repo_id])) {
            $file_name = $this->source_path . $this->repo_list[$repo_id]['locales'];
            if (file_exists($file_name)) {
                $locales = file($file_name, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
                sort($locales);
            }
        }

        return $locales;
    }
}
