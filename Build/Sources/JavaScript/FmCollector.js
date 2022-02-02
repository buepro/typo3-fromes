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

  renderClearIcon() {
    let clearNode = this.querySelector('[data-fromes="clear"]');
    clearNode.classList.add('invisible');
    if (this.items && this.items.length > 0) {
      clearNode.classList.remove('invisible');
    }
  }

  renderDefaultContent() {
    if (!this.items || this.items.length === 0) {
      const node = this._itemTemplate.cloneNode(true);
      node.classList.add('fmc-default');
      this._itemsParent.appendChild(node);
    }
  }

  render() {
    this.renderClearIcon()
    FmAbstractList.prototype.render.call(this);
    this.renderDefaultContent();
  }

  handleClearClickEvent(event) {
    this.items = [];
    event.stopPropagation();
  }
}

customElements.define('fm-collector', FmCollector);
