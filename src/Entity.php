<?php

namespace Wikidata;

use Illuminate\Support\Collection;
use Wikidata\Property;

class Entity
{
  /**
   * @var string Entity Id
   */
  public string $id;

  /**
   * @var string Entity language
   */
  public string $lang;

  /**
   * @var string Entity label
   */
  public string $label;

  /**
   * @var string A link to a Wikipedia article about this entity
   */
  public string $wiki_url = '';

  /**
   * @var string[] List of entity aliases
   */
  public $aliases = [];

  /**
   * @var string Entity description
   */
  public string $description;

  public Collection $properties;

  /**
   * @param array $data
   * @param string $lang
   */
  public function __construct(array $data, string $lang)
  {
    $this->lang = $lang;
    $this->properties = new Collection();
    $this->parseData($data);
  }

  /**
   * Parse input data
   *
   * @param array $data
   */
  private function parseData(array $data)
  {
    $lang = $this->lang;
    $site = $lang . 'wiki';

    $this->id = $data['id'];
    $this->label = isset($data['labels'][$lang]) ? $data['labels'][$lang]['value'] : '';
    $this->description = isset($data['descriptions'][$lang]) ? $data['descriptions'][$lang]['value'] : '';
    $this->wiki_url = isset($data['sitelinks'][$site]) ? $data['sitelinks'][$site]['url'] : '';
    $this->aliases = isset($data['aliases'][$lang]) ? collect($data['aliases'][$lang])->pluck('value')->toArray() : [];
  }

  /**
   * Parse entity properties from sparql result
   *
   * @param Collection $data
   */
  public function parseProperties(array|Collection $data)
  {
    $collection = (new Collection($data))->groupBy('prop');
    $this->properties = $collection->mapWithKeys(function ($item) {
      $property = new Property($item);

      return [$property->id => $property];
    });
  }

  public function toArray()
  {
    return [
      'id' => $this->id,
      'lang' => $this->lang,
      'label' => $this->label,
      'description' => $this->description,
      'wiki_url' => $this->wiki_url,
      'aliases' => $this->aliases,
      'properties' => $this->properties,
    ];
  }
}
