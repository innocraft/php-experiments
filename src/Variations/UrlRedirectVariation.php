<?php
/**
 * InnoCraft Ltd - We are the makers of Piwik Analytics, the leading open source analytics platform.
 *
 * @link https://www.innocraft.com
 * @license https://www.gnu.org/licenses/lgpl-3.0.en.html LGPL v3.0
 */

namespace InnoCraft\Experiments\Variations;

class UrlRedirectVariation extends StandardVariation {

    /**
     * @var string|int
     */
    private $experimentName;

    /**
     * A variation that can perform a redirect when passing a URL and executing the run method.
     *
     * @param string|int $experimentName
     * @param array $variation
     */
    public function __construct($experimentName, $variation)
    {
        parent::__construct($variation);

        $this->experimentName = $experimentName;
    }

    public function getUrl()
    {
        if (isset($this->variation['url'])) {
            return $this->variation['url'];
        }

        return '';
    }

    public function getUrlWithExperimentParameters()
    {
        $url = $this->getUrl();

        if (false === strpos($url, '?')) {
            $url .= '?';
        } else {
            $url .= '&';
        }

        $url .= 'pk_abe=' . urlencode($this->experimentName) . '&pk_abv=' . urlencode($this->getName());

        return $url;
    }

    /**
     * @inheritdoc
     */
    public function run()
    {
        if (!headers_sent()) {
            // for now we do not throw an exception here if headers were already sent as it could break
            // users app and in worst case people would simply instead see the original version if headers
            // were already sent so should be fine for now
            $url = $this->getUrlWithExperimentParameters();
            header('Location: ' . $url, true, 302);
            exit;
        }
    }
}