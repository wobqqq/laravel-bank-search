<?php

declare(strict_types=1);

namespace App\Services\PageParsingService;

final class ComunitateBancaTransilvaniaPageParsingService extends BasePageParsingService
{
    protected const array CLASSES_TO_EXCLUDE = [
        '.bt-form-sticky-button',
        '.bt-page-sticky',
        'main .container .bt-csr-product-box-inner-column',
        '.listing-projects',
        '.modal-search__content-top',
        '[data-ajax-partial="@content"]',
        '.bt-homepage-articles-row',
        '.bt-content-menu',
        '.bt-menu-links',
        '.modal-search',
        '.bt-modal',
        '.bt-breadcrumbs',
        '.breadcrumb',
        '.filters',
        '.bt-header-pj-slide-comunitate',
    ];
}
