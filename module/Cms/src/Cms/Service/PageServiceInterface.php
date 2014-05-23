<?php
namespace Cms\Service;

interface PageServiceInterface
{
    /**
     * Should return all the records 
     *
     * @return array|\Traversable
     */
    public function findAll();

    /**
     * Should return a single record
     *
     * @param  int $id Identifier of the Record that should be returned
     * @return \Cms\Entity\Page
     */
    public function find($id);
    
    /**
     * Should return a single record
     *
     * @param  int $slug Identifier of the Record that should be returned
     * @return \Cms\Entity\Page
     */
    public function findByUri($slug);
    
    /**
     * Should delete a single record
     *
     * @param  int $id Identifier of the Record that should be deleted
     * @return \Cms\Entity\Page
     */
    public function delete($id);
    
    /**
     * Should save a single record
     *
     * @param  \Cms\Model\Page $record object that should be saved
     * @return \Cms\Entity\Page
     */
    public function save(\Cms\Entity\Page $record);
}