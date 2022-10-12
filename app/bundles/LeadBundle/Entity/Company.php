<?php

namespace Mautic\LeadBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Mautic\ApiBundle\Serializer\Driver\ApiMetadataDriver;
use Mautic\CoreBundle\Doctrine\Mapping\ClassMetadataBuilder;
use Mautic\CoreBundle\Entity\FormEntity;
use Mautic\LeadBundle\Model\FieldModel;
use Mautic\UserBundle\Entity\User;

class Company extends FormEntity implements CustomFieldEntityInterface, IdentifierFieldEntityInterface
{
    use CustomFieldEntityTrait;

    public const FIELD_ALIAS = 'company';

    /**
     * @var int
     */
    private $id;

    private int $score = 0;

    private ?\Mautic\UserBundle\Entity\User $owner = null;

    /**
     * @var mixed[]
     */
    private array $socialCache = [];

    private $email;

    private $address1;

    private $address2;

    private $phone;

    private $city;

    private $state;

    private $zipcode;

    private $country;

    private $name;

    private $website;

    private $industry;

    private $description;

    public function __clone()
    {
        $this->id = null;

        parent::__clone();
    }

    /**
     * @return mixed[]
     */
    public function getSocialCache()
    {
        return $this->socialCache;
    }

    /**
     * @param mixed[] $cache
     */
    public function setSocialCache($cache)
    {
        $this->socialCache = $cache;
    }

    public static function loadMetadata(ORM\ClassMetadata $metadata)
    {
        $builder = new ClassMetadataBuilder($metadata);
        $builder->setTable('companies')
            ->setCustomRepositoryClass(CompanyRepository::class);

        $builder->createField('id', 'integer')
            ->isPrimaryKey()
            ->generatedValue()
            ->build();

        $builder->createField('socialCache', 'array')
            ->columnName('social_cache')
            ->nullable()
            ->build();

        $builder->createManyToOne('owner', \Mautic\UserBundle\Entity\User::class)
            ->cascadeMerge()
            ->addJoinColumn('owner_id', 'id', true, false, 'SET NULL')
            ->build();

        $builder->createField('score', 'integer')
            ->nullable()
            ->build();

        self::loadFixedFieldMetadata(
            $builder,
            [
                'email',
                'address1',
                'address2',
                'phone',
                'city',
                'state',
                'zipcode',
                'country',
                'name',
                'website',
                'industry',
                'description',
            ],
            FieldModel::$coreCompanyFields
        );
    }

    /**
     * Prepares the metadata for API usage.
     *
     * @param $metadata
     */
    public static function loadApiMetadata(ApiMetadataDriver $metadata)
    {
        $metadata->setGroupPrefix('companyBasic')
            ->addListProperties(
                [
                    'id',
                    'name',
                    'email',
                    'address1',
                    'address2',
                    'phone',
                    'city',
                    'state',
                    'zipcode',
                    'country',
                    'website',
                    'industry',
                    'description',
                    'score',
                ]
            )
            ->setGroupPrefix('company')
            ->addListProperties(
                [
                    'id',
                    'fields',
                    'score',
                ]
            )
            ->build();
    }

    public static function getDefaultIdentifierFields(): array
    {
        return [
            'companyname',
            'companyemail',
            'companywebsite',
            'city',
            'state',
            'country',
        ];
    }

    /**
     * @param string $prop
     * @param mixed  $val
     */
    protected function isChanged($prop, $val)
    {
        $getter  = 'get'.ucfirst($prop);
        $current = $this->$getter();
        if ('owner' == $prop) {
            if ($current && !$val) {
                $this->changes['owner'] = [$current->getName().' ('.$current->getId().')', $val];
            } elseif (!$current && $val) {
                $this->changes['owner'] = [$current, $val->getName().' ('.$val->getId().')'];
            } elseif ($current && $val && $current->getId() != $val->getId()) {
                $this->changes['owner'] = [
                    $current->getName().'('.$current->getId().')',
                    $val->getName().'('.$val->getId().')',
                ];
            }
        } else {
            parent::isChanged($prop, $val);
        }
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Get the primary identifier for the company.
     *
     * @return string
     */
    public function getPrimaryIdentifier()
    {
        if ($name = $this->getName()) {
            return $name;
        } elseif (!empty($this->fields['core']['companyemail']['value'])) {
            return $this->fields['core']['companyemail']['value'];
        }
    }

    /**
     * @return Company
     */
    public function setOwner(User $owner = null)
    {
        $this->isChanged('owner', $owner);
        $this->owner = $owner;

        return $this;
    }

    /**
     * @return User
     */
    public function getOwner()
    {
        return $this->owner;
    }

    /**
     * Returns the user to be used for permissions.
     */
    public function getPermissionUser(): \Mautic\UserBundle\Entity\User|int
    {
        return $this->getOwner() ?? $this->getCreatedBy();
    }

    /**
     * @param User $score
     *
     * @return Company
     */
    public function setScore($score)
    {
        $score = (int) $score;

        $this->isChanged('score', $score);
        $this->score = $score;

        return $this;
    }

    /**
     * @return int
     */
    public function getScore()
    {
        return $this->score;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return Company
     */
    public function setName(mixed $name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @return Company
     */
    public function setEmail(mixed $email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getAddress1()
    {
        return $this->address1;
    }

    /**
     * @return Company
     */
    public function setAddress1(mixed $address1)
    {
        $this->address1 = $address1;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getAddress2()
    {
        return $this->address2;
    }

    /**
     * @return Company
     */
    public function setAddress2(mixed $address2)
    {
        $this->address2 = $address2;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * @return Company
     */
    public function setPhone(mixed $phone)
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * @return Company
     */
    public function setCity(mixed $city)
    {
        $this->city = $city;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * @return Company
     */
    public function setState(mixed $state)
    {
        $this->state = $state;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getZipcode()
    {
        return $this->zipcode;
    }

    /**
     * @return Company
     */
    public function setZipcode(mixed $zipcode)
    {
        $this->zipcode = $zipcode;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * @return Company
     */
    public function setCountry(mixed $country)
    {
        $this->country = $country;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getWebsite()
    {
        return $this->website;
    }

    /**
     * @return Company
     */
    public function setWebsite(mixed $website)
    {
        $this->website = $website;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getIndustry()
    {
        return $this->industry;
    }

    /**
     * @return Company
     */
    public function setIndustry(mixed $industry)
    {
        $this->industry = $industry;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @return Company
     */
    public function setDescription(mixed $description)
    {
        $this->description = $description;

        return $this;
    }
}
