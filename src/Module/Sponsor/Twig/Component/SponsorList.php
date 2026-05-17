<?php

declare(strict_types=1);

namespace App\Module\Sponsor\Twig\Component;

use App\Module\Sponsor\Entity\Sponsor;
use App\Module\Sponsor\Repository\SponsorRepository;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(name: 'Sponsor:List', template: '@Sponsor/components/List.html.twig')]
final class SponsorList
{
    public function __construct(
        private readonly SponsorRepository $sponsors,
    ) {
    }

    /**
     * @return list<Sponsor>
     */
    public function getSponsors(): array
    {
        return $this->sponsors->findActiveOrdered();
    }
}
