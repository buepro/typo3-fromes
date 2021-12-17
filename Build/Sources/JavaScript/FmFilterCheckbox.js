import { html, css, LitElement } from 'lit';

export class FmFilterCheckbox extends LitElement {
  static get properties() {
    return {
      items: { type: Array },
      itemHtml: { type: Node }
    };
  }

  constructor() {
    super();
    this.itemHtml = this.children[0].cloneNode(true);
    this.items = [
      { name: 'name1', value: 1, label: 'Default label 1' },
      { name: 'name2', value: 2, label: 'Default label 2' }
    ];
  }

  render() {
    let component = this;
    return this.items.map(function (item) {
      let node = component.itemHtml.cloneNode(true);
      let input = node.getElementsByTagName('input')[0];
      let label = node.getElementsByTagName('label')[0];
      if (item.name !== undefined) {
        input.name = item.name;
      }
      if (item.value !== undefined) {
        input.value = item.value;
      }
      if (item.label !== undefined) {
        label.textContent = item.label;
      }
      return node;
    })
  }
}

customElements.define('fm-filter-checkbox', FmFilterCheckbox);
