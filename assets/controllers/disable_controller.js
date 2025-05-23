import { Controller } from "@hotwired/stimulus"

export default class extends Controller {
  static values = {
    disabled: { default: false, type: Boolean },
    withDisabled: String,
    withEnabled: String,
  }

  connect() {
    this.element.dataset["action"] = "submit->disable#disableForm"

    if (this.disabledValue) {
      this.disableForm()
    }

    if (!this.hasWithValue) {
      this.withValue = "Processing..."
    }
  }

  disableForm() {
    this.submitButtons().forEach((button) => {
      button.disabled = true
      button.value = this.withDisabledValue
    })
  }

  enabledForm() {
    this.submitButtons().forEach((button) => {
      button.disabled = false
      button.value = this.withEnabledValue
    })
  }

  submitButtons() {
    return this.element.querySelectorAll("input[type='submit']")
  }

  applyTo() {
    this.enabledForm()
  }
}
