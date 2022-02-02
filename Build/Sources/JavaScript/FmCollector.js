import { FmAbstractList } from "./FmAbstractList";

/**
 * Collected items can be cleared.
 */
export class FmCollector extends FmAbstractList {
  constructor() {
    super();
    this.querySelector('[data-fromes="clear"]').addEventListener('click', this.handleClearClickEvent.bind(this));
  }

  getItemNode(item, counter) {
    let node = this._itemTemplate.cloneNode(true);
    node.querySelector('[data-fromes="label"]').textContent = item.label;
    return node;
  }

  render() {
    let clearNode = this.querySelector('[data-fromes="clear"]');
    clearNode.classList.add('invisible');
    if (this.items && this.items.length > 0) {
      clearNode.classList.remove('invisible');
    }
    FmAbstractList.prototype.render.call(this);
  }

  handleClearClickEvent(event) {
    this.items = [];
    event.stopPropagation();
  }
}

customElements.define('fm-collector', FmCollector);
