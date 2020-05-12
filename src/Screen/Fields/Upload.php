<?php

declare(strict_types=1);

namespace Orchid\Screen\Fields;

use Illuminate\Support\Arr;
use Orchid\Attachment\Models\Attachment;
use Orchid\Platform\Dashboard;
use Orchid\Screen\Field;
use Orchid\Support\Assert;
use Orchid\Support\Init;

/**
 * Class Upload.
 *
 * @method Upload form($value = true)
 * @method Upload formaction($value = true)
 * @method Upload formenctype($value = true)
 * @method Upload formmethod($value = true)
 * @method Upload formnovalidate($value = true)
 * @method Upload formtarget($value = true)
 * @method Upload name(string $value = null)
 * @method Upload placeholder(string $value = null)
 * @method Upload value($value = true)
 * @method Upload help(string $value = null)
 * @method Upload storage($value = true)
 * @method Upload parallelUploads($value = true)
 * @method Upload maxFileSize($value = true)
 * @method Upload maxFiles($value = true)
 * @method Upload acceptedFiles($value = true)
 * @method Upload resizeQuality($value = true)
 * @method Upload resizeWidth($value = true)
 * @method Upload resizeHeight($value = true)
 * @method Upload popover(string $value = null)
 * @method Upload groups($value = true)
 * @method Upload media($value = true)
 * @method Upload closeOnAdd($value = true)
 * @method Upload title(string $value = null)
 */
class Upload extends Field
{
    /**
     * @var string
     */
    protected $view = 'platform::fields.upload';

    /**
     * All attributes that are available to the field.
     *
     * @var array
     */
    protected $attributes = [
        'value'           => null,
        'multiple'        => true,
        'parallelUploads' => 10,
        'maxFileSize'     => null,
        'maxFiles'        => 9999,
        'acceptedFiles'   => null,
        'resizeQuality'   => 0.8,
        'resizeWidth'     => null,
        'resizeHeight'    => null,
        'media'           => false,
        'closeOnAdd'      => false,
    ];

    /**
     * Attributes available for a particular tag.
     *
     * @var array
     */
    protected $inlineAttributes = [
        'accept',
        'form',
        'formaction',
        'formenctype',
        'formmethod',
        'formnovalidate',
        'formtarget',
        'multiple',
        'name',
        'placeholder',
        'required',
        'value',
        'groups',
        'storage',
        'media',
        'closeOnAdd',
    ];

    /**
     * Upload constructor.
     */
    public function __construct()
    {
        // Set max file size
        $this->addBeforeRender(function () {
            $maxFileSize = $this->get('maxFileSize');

            $serverMaxFileSize = Init::maxFileUpload(Init::MB);

            if ($maxFileSize === null) {
                $this->set('maxFileSize', $serverMaxFileSize);

                return;
            }

            throw_if(
                $maxFileSize > $serverMaxFileSize,
                \RuntimeException::class,
                'Cannot set the desired maximum file size. This contradicts the settings specified in .ini');
        });

        // set load relation attachment
        $this->addBeforeRender(function () {
            $value = Arr::wrap($this->get('value'));

            if (! Assert::isIntArray($value)) {
                return;
            }

            /** @var Attachment $attach */
            $attach = Dashboard::model(Attachment::class);

            $value = $attach::whereIn('id', $value)->orderBy('sort')->get()->toArray();

            $this->set('value', $value);
        });
    }
}
