import { FmAbstractItems } from "./FmAbstractItems";

export class FmFilterCheckbox extends FmAbstractItems {
  getItemNode(item, index) {
    let node = this._itemTemplate.cloneNode(true);
    let input = node.getElementsByTagName('input')[0];
    let label = node.getElementsByTagName('label')[0];
    label.htmlFor = input.name = input.id = `${this.id}-${item.id}`;
    input.value = item.id;
    if (item.label !== undefined) {
      label.textContent = item.label;
    }
    return node;
  }

  get value() {
    const inputs = this.querySelectorAll('input:checked');
    const result = [];
    for (const input of inputs) {
      result.push(input.value);
    }
    return result;
  }
}

customElements.define('fm-filter-checkbox', FmFilterCheckbox);
