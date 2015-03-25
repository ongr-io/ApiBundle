<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\ApiBundle\Tests\Functional\Fixtures\Bundles\Acme\TestBundle\Document;

use ONGR\ElasticsearchBundle\Annotation as ES;
use ONGR\ElasticsearchBundle\Document\AbstractDocument;

/**
 * Class Person Document.
 *
 * @ES\Document(type="person")
 */
class PersonDocument extends AbstractDocument
{
    /**
     * @var string
     *
     * @ES\Property(name="name", type="string", index="not_analyzed")
     */
    protected $name;

    /**
     * @var string
     *
     * @ES\Property(name="surname", type="string", index="not_analyzed")
     */
    protected $surname;

    /**
     * @var string
     *
     * @ES\Property(name="nickname", type="string")
     */
    protected $nickname;

    /**
     * @var string[]
     *
     * @ES\Property(name="phones", type="string", multiple=true)
     */
    protected $phones;

    /**
     * @var string
     *
     * @ES\Property(name="city", type="string")
     */
    protected $city;

    /**
     * @var string
     *
     * @ES\Property(name="address", type="string")
     */
    protected $address;

    /**
     * @var int
     *
     * @ES\Property(name="age", type="integer")
     */
    protected $age;

    /**
     * @var string
     *
     * @ES\Property(name="provider", type="string")
     */
    protected $provider;

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getSurname()
    {
        return $this->surname;
    }

    /**
     * @param string $surname
     */
    public function setSurname($surname)
    {
        $this->surname = $surname;
    }

    /**
     * @return string
     */
    public function getNickname()
    {
        return $this->nickname;
    }

    /**
     * @param string $nickname
     */
    public function setNickname($nickname)
    {
        $this->nickname = $nickname;
    }

    /**
     * @return \string[]
     */
    public function getPhones()
    {
        return $this->phones;
    }

    /**
     * @param \string[] $phones
     */
    public function setPhones($phones)
    {
        $this->phones = $phones;
    }

    /**
     * @return string
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * @param string $city
     */
    public function setCity($city)
    {
        $this->city = $city;
    }

    /**
     * @return string
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * @param string $address
     */
    public function setAddress($address)
    {
        $this->address = $address;
    }

    /**
     * @return int
     */
    public function getAge()
    {
        return $this->age;
    }

    /**
     * @param int $age
     */
    public function setAge($age)
    {
        $this->age = $age;
    }

    /**
     * @return string
     */
    public function getProvider()
    {
        return $this->provider;
    }

    /**
     * @param string $provider
     */
    public function setProvider($provider)
    {
        $this->provider = $provider;
    }
}
