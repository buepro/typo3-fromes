import { html, css, LitElement } from 'lit';

export class FmFilter extends LitElement {
  static properties = {
    config: { type: Object, attribute: 'data-config' }
  };

  constructor() {
    super();
    this.addEventListener('change', this.handleChangeEvent);
  }

  _subfilters = [];

  createRenderRoot() {
    return this;
  }

  initializeFilters() {
    for (const config of this.config['jsonFilter']) {
      let subfilter = this.querySelector('[data-filter-id="' + config.id + '"]');
      if (subfilter !== null) {
        subfilter.items = config.items;
        this._subfilters.push(subfilter);
      }
    }
  }

  render() {
    this.initializeFilters();
    return this.children;
  }

  handleChangeEvent(event) {
    const filterStatus = [];
    for (const subfilter of this._subfilters) {
      filterStatus.push({ id: subfilter.id, value: subfilter.value });
    }
    event.stopPropagation();
    this.createServerRequest(filterStatus);
  }

  createServerRequest(data) {
    const url = window.location.protocol + '//' + window.location.host + window.location.pathname;
    console.log(url);
    fetch(url, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Fromes' : this.config['accessToken']
      },
      body: JSON.stringify({ data }),
    })
      .then(response => response.json())
      .then(data => {
        console.log('Success:', data);
      })
      .catch((error) => {
        console.error('Error:', error);
      });
  }
}

customElements.define('fm-filter', FmFilter);
