import { Controller } from '@hotwired/stimulus';

/**
 * Two-axis client-side filter for the /programma race list:
 *   - discipline (road / track / cyclocross)
 *   - category   (miniemen / aspiranten / nieuwelingen / junioren)
 *
 * Cards carry `data-discipline` and `data-categories="comma,separated"`
 * attributes. The controller hides the cards that don't match the
 * current state and toggles a "no results" message accordingly.
 */
export default class extends Controller {
    static targets = ['card', 'group', 'empty'];

    initialize() {
        this._discipline = 'all';
        this._category = 'all';
    }

    filterDiscipline(event) {
        this._discipline = event.currentTarget.dataset.discipline || 'all';
        this._syncChipActive(event.currentTarget);
        this._apply();
    }

    filterCategory(event) {
        this._category = event.currentTarget.dataset.category || 'all';
        this._syncChipActive(event.currentTarget);
        this._apply();
    }

    _syncChipActive(button) {
        const group = button.parentElement;
        if (!group) return;
        group.querySelectorAll('.programme-toolbar__chip').forEach((chip) => {
            chip.classList.toggle('programme-toolbar__chip--active', chip === button);
        });
    }

    _apply() {
        let visibleCount = 0;
        this.cardTargets.forEach((card) => {
            const cardDiscipline = card.dataset.discipline;
            const cardCategories = (card.dataset.categories || '').split(',').filter(Boolean);

            const matchesDiscipline = this._discipline === 'all' || cardDiscipline === this._discipline;
            const matchesCategory = this._category === 'all' || cardCategories.includes(this._category);
            const visible = matchesDiscipline && matchesCategory;

            card.classList.toggle('d-none', !visible);
            if (visible) visibleCount += 1;
        });

        // Hide month groups that have no visible card.
        if (this.hasGroupTarget) {
            this.groupTargets.forEach((group) => {
                const anyVisible = Array.from(group.querySelectorAll('[data-programme-filter-target="card"]'))
                    .some((c) => !c.classList.contains('d-none'));
                group.previousElementSibling?.classList.toggle('d-none',
                    group.previousElementSibling?.classList.contains('programme-month') && !anyVisible);
                group.classList.toggle('d-none', !anyVisible);
            });
        }

        if (this.hasEmptyTarget) {
            this.emptyTarget.classList.toggle('d-none', visibleCount !== 0);
        }
    }
}
