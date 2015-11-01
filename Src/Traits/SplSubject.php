<?php
/**
 * SplSubject trait
 *
 * @since      0.50.0
 * @package    kabar
 * @subpackage traits
 * @author     Gniewomir Świechowski <gniewomir.swiechowski@gmail.com>
 * @license    http://www.gnu.org/licenses/gpl-3.0.txt GNU GENERAL PUBLIC LICENSE Version 3
 */

namespace kabar\Traits;

trait SplSubject
{
    /**
     * Object observers
     * @var array<\SplObserver>
     */
    private $observers;

    /**
     * Attach observer to object
     * @param  \SplObserver $observer
     * @return void
     */
    public function attach(\SplObserver $observer)
    {
        /**
         * Not every subject will be observed, so lets not create object storage upfront
         */
        if (!$this->observers instanceof \SplObjectStorage) {
            $this->observers = new \SplObjectStorage();
        }

        $this->observers->attach($observer);
    }

    /**
     * Detach observer from object
     * @param  \SplObserver $observer
     * @return void
     */
    public function detach(\SplObserver $observer)
    {
        $this->observers->detach($observer);
    }

    /**
     * Notify observers about object change
     * @return void
     */
    public function notify()
    {
        foreach ($this->observers as $observer) {
            $observer->update($this);
        }
    }
}
