<?php

namespace MauticPlugin\MauticSocialBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Mautic\ApiBundle\Serializer\Driver\ApiMetadataDriver;
use Mautic\CoreBundle\Doctrine\Mapping\ClassMetadataBuilder;
use Mautic\LeadBundle\Entity\Lead as TheLead;

/**
 * Class TweetStat.
 */
class TweetStat
{
    private ?int $id = null;

    /**
     * ID of the tweet from Twitter.
     */
    private ?string $twitterTweetId = null;

    private ?\MauticPlugin\MauticSocialBundle\Entity\Tweet $tweet = null;

    private ?TheLead $lead = null;

    private ?string $handle = null;

    private ?\DateTime $dateSent = null;

    private bool $isFailed = false;

    private int $retryCount = 0;

    private ?string $source = null;

    private ?int $sourceId = null;

    private int $favoriteCount = 0;

    private int $retweetCount = 0;

    private array $responseDetails = [];

    public static function loadMetadata(ORM\ClassMetadata $metadata)
    {
        $builder = new ClassMetadataBuilder($metadata);

        $builder->setTable('tweet_stats')
            ->setCustomRepositoryClass(TweetStatRepository::class)
            ->addIndex(['tweet_id', 'lead_id'], 'stat_tweet_search')
            ->addIndex(['lead_id', 'tweet_id'], 'stat_tweet_search2')
            ->addIndex(['is_failed'], 'stat_tweet_failed_search')
            ->addIndex(['source', 'source_id'], 'stat_tweet_source_search')
            ->addIndex(['favorite_count'], 'favorite_count_index')
            ->addIndex(['retweet_count'], 'retweet_count_index')
            ->addIndex(['date_sent'], 'tweet_date_sent')
            ->addIndex(['twitter_tweet_id'], 'twitter_tweet_id_index');

        $builder->addId();

        $builder->createManyToOne('tweet', 'Tweet')
            ->inversedBy('stats')
            ->addJoinColumn('tweet_id', 'id', true, false, 'SET NULL')
            ->build();

        $builder->createField('twitterTweetId', 'string')
            ->columnName('twitter_tweet_id')
            ->nullable()
            ->build();

        $builder->addLead(true, 'SET NULL');

        $builder->createField('handle', 'string')
            ->build();

        $builder->createField('dateSent', 'datetime')
            ->columnName('date_sent')
            ->nullable()
            ->build();

        $builder->createField('isFailed', 'boolean')
            ->columnName('is_failed')
            ->nullable()
            ->build();

        $builder->createField('retryCount', 'integer')
            ->columnName('retry_count')
            ->nullable()
            ->build();

        $builder->createField('source', 'string')
            ->nullable()
            ->build();

        $builder->createField('sourceId', 'integer')
            ->columnName('source_id')
            ->nullable()
            ->build();

        $builder->addNullableField('favoriteCount', 'integer', 'favorite_count');
        $builder->addNullableField('retweetCount', 'integer', 'retweet_count');
        $builder->addNullableField('responseDetails', 'json_array', 'response_details');
    }

    /**
     * Prepares the metadata for API usage.
     *
     * @param $metadata
     */
    public static function loadApiMetadata(ApiMetadataDriver $metadata)
    {
        $metadata->setGroupPrefix('stat')
            ->addProperties(
                [
                    'id',
                    'tweetId',
                    'handle',
                    'dateSent',
                    'isFailed',
                    'retryCount',
                    'favoriteCount',
                    'retweetCount',
                    'source',
                    'sourceId',
                    'lead',
                    'tweet',
                    'responseDetails',
                ]
            )
            ->build();
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string|null
     */
    public function getTwitterTweetId()
    {
        return $this->twitterTweetId;
    }

    /**
     * @param string $twitterTweetId
     *
     * @return $this
     */
    public function setTwitterTweetId($twitterTweetId)
    {
        $this->twitterTweetId = $twitterTweetId;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getDateSent()
    {
        return $this->dateSent;
    }

    public function setDateSent(mixed $dateSent)
    {
        $this->dateSent = $dateSent;
    }

    /**
     * @return Tweet
     */
    public function getTweet()
    {
        return $this->tweet;
    }

    /**
     * @param mixed $tweet
     */
    public function setTweet(Tweet $tweet = null)
    {
        $this->tweet = $tweet;
    }

    /**
     * @return TheLead
     */
    public function getLead()
    {
        return $this->lead;
    }

    /**
     * @param mixed $lead
     */
    public function setLead(TheLead $lead = null)
    {
        $this->lead = $lead;
    }

    /**
     * @return mixed
     */
    public function getRetryCount()
    {
        return $this->retryCount;
    }

    public function setRetryCount(mixed $retryCount)
    {
        $this->retryCount = $retryCount;
    }

    public function retryCountUp()
    {
        $this->setRetryCount($this->getRetryCount() + 1);
    }

    /**
     * @return int
     */
    public function getFavoriteCount()
    {
        return $this->favoriteCount;
    }

    /**
     * @param int $favoriteCount
     *
     * @return $this
     */
    public function setFavoriteCount($favoriteCount)
    {
        $this->favoriteCount = $favoriteCount;

        return $this;
    }

    /**
     * @return int
     */
    public function getRetweetCount()
    {
        return $this->retweetCount;
    }

    /**
     * @param int $retweetCount
     *
     * @return $this
     */
    public function setRetweetCount($retweetCount)
    {
        $this->retweetCount = $retweetCount;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getIsFailed()
    {
        return $this->isFailed;
    }

    public function setIsFailed(mixed $isFailed)
    {
        $this->isFailed = $isFailed;
    }

    /**
     * @return mixed
     */
    public function isFailed()
    {
        return $this->getIsFailed();
    }

    /**
     * @return string|null
     */
    public function getHandle()
    {
        return $this->handle;
    }

    public function setHandle(mixed $handle)
    {
        $this->handle = $handle;
    }

    /**
     * @return mixed
     */
    public function getSource()
    {
        return $this->source;
    }

    public function setSource(mixed $source)
    {
        $this->source = $source;
    }

    /**
     * @return mixed
     */
    public function getSourceId()
    {
        return $this->sourceId;
    }

    public function setSourceId(mixed $sourceId)
    {
        $this->sourceId = (int) $sourceId;
    }

    /**
     * @return mixed
     */
    public function getResponseDetails()
    {
        return $this->responseDetails;
    }

    /**
     * @return Stat
     */
    public function setResponseDetails(mixed $responseDetails)
    {
        $this->responseDetails = $responseDetails;

        return $this;
    }
}
