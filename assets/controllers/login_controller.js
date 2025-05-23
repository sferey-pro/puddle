import { Controller } from "@hotwired/stimulus"

export default class extends Controller {
  static targets = ["emailField", "passwordField", "emailDisplay"]

  nextStep() {
    let emailValue = this.emailFieldTarget.querySelector("input").value
    if (emailValue) {
      this.emailDisplayTarget.innerHTML = emailValue
      this.toogleStep()
    }
  }

  previousStep() {
    this.toogleStep()
  }

  toogleStep() {
    this.emailFieldTarget.hidden = !this.emailFieldTarget.hidden
    this.passwordFieldTarget.hidden = !this.passwordFieldTarget.hidden
  }
}
