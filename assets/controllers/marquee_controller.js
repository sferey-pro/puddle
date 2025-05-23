// /home/sferey/puddle/assets/controllers/marquee_controller.js
import { Controller } from "@hotwired/stimulus";

export default class extends Controller {
  static values = {
    text: String,
    maxWidth: { type: Number, default: 220 },
    speed: { type: Number, default: 50 }, // Vitesse en pixels/seconde, ajoutée ici
  };
  static targets = ["anchor"];

  connect() {
    // Appliquer la max-width au conteneur dès la connexion
    this.element.style.maxWidth = `${this.maxWidthValue}px`;

    // Lier les méthodes pour conserver le contexte 'this'
    this.boundUpdateMarquee = this.updateMarquee.bind(this);

    // Mettre à jour le marquee initialement et écouter les redimensionnements de la fenêtre
    this.updateMarquee();
    window.addEventListener("resize", this.boundUpdateMarquee);
  }

  disconnect() {
    // Nettoyer les écouteurs d'événements lors de la déconnexion du contrôleur
    window.removeEventListener("resize", this.boundUpdateMarquee);
  }

  // Appelé quand la valeur data-marquee-text-value change
  textValueChanged() {
    this.updateMarquee();
  }

  // Appelé quand la valeur data-marquee-max-width-value change
  maxWidthValueChanged() {
    this.element.style.maxWidth = `${this.maxWidthValue}px`;
    this.updateMarquee();
  }

  // Appelé quand la valeur data-marquee-speed-value change
  speedValueChanged() {
    // Si la vitesse change, il faut recalculer la durée de l'animation
    this.updateMarquee();
  }

  updateMarquee() {
    if (!this.hasAnchorTarget) {
      console.error(
        "Marquee Controller: L'élément <a> avec data-marquee-target='anchor' est introuvable."
      );
      return;
    }
    const anchorElement = this.anchorTarget;
    anchorElement.innerHTML = ""; // Vider le contenu précédent

    let textWidthWithPadding;
    // Pour déterminer si le défilement est nécessaire, nous devons mesurer la largeur
    // du span *avec* les styles qu'il aurait si le défilement était actif (notamment le padding-right).
    // Appliquer temporairement la classe 'is-scrolling-active' au conteneur parent
    // pour que le 'contentSpan' hérite des styles conditionnels pour la mesure.
    this.element.classList.add("is-scrolling-active");

    // Créer un span temporaire juste pour la mesure de la largeur d'un segment
    const tempSpanForMeasurement = document.createElement("span");
    tempSpanForMeasurement.textContent = this.textValue || "";
    anchorElement.appendChild(tempSpanForMeasurement); // Doit être dans le DOM pour scrollWidth

    // Mesurer la largeur du span. scrollWidth inclut le contenu et le padding pour un inline-block.
    textWidthWithPadding = tempSpanForMeasurement.scrollWidth;
    anchorElement.innerHTML = ""; // Vider le span temporaire

    // Retirer la classe temporaire immédiatement après la mesure.
    // La décision finale de remettre la classe se fera plus bas.
    this.element.classList.remove("is-scrolling-active");

    const containerWidth = this.element.clientWidth;
    // Le défilement est nécessaire si la largeur du texte (avec son padding potentiel) dépasse celle du conteneur
    const shouldScroll = textWidthWithPadding > containerWidth;

    if (shouldScroll) {
      // Réappliquer la classe pour activer les styles de défilement (padding sur span, animation sur a)
      this.element.classList.add("is-scrolling-active");

      // Calculer la distance totale à parcourir dans un cycle d'animation
      // C'est la largeur du conteneur + la largeur du texte (avec padding)
      const distanceToTravel = containerWidth + textWidthWithPadding;

      // Créer deux spans pour le défilement continu
      const span1 = document.createElement("span");
      span1.textContent = this.textValue || "";
      const span2 = document.createElement("span");
      span2.textContent = this.textValue || "";
      anchorElement.appendChild(span1);
      anchorElement.appendChild(span2);

      // La distance à parcourir pour un cycle d'animation est la largeur d'un segment de texte
      const distanceToTravelForOneSegment = textWidthWithPadding;

      // S'assurer que speedValue est un nombre positif pour éviter la division par zéro ou NaN
      // Utiliser la valeur par défaut de speed si this.speedValue n'est pas valide
      const currentSpeed =
        this.hasSpeedValue && this.speedValue > 0
          ? this.speedValue
          : this.constructor.values.speed.default;

      // La ligne `const duration = (distanceToTravel / currentSpeed) / 1.2;` a été modifiée.
      // Le `/ 1.2` était un ajustement manuel. Pour une vitesse standard :
      const duration = distanceToTravelForOneSegment / currentSpeed; // Durée en secondes

      // Définir les variables CSS pour l'animation sur l'élément <a>
      // Ces variables seront utilisées dans les keyframes SCSS
      anchorElement.style.setProperty(
        "--text-segment-width",
        `${textWidthWithPadding}px`
      );

      anchorElement.style.setProperty("--animation-duration", `${duration}s`); // Passer la durée calculée
      // Position initiale : le texte commence juste à droite du conteneur (hors champ)
      // Cette transformation de base est combinée avec les transformations de l'animation CSS.
      anchorElement.style.transform = "translateX(var(--container-width))";

      // Avec deux spans, l'élément <a> peut commencer à translateX(0)
      // L'animation CSS gérera le déplacement pour la boucle.
      anchorElement.style.transform = "translateX(0)";

      // Activer l'animation CSS
      anchorElement.style.animationName = "marquee-loop"; // Nom de l'animation CSS
      // L'animation démarre et tourne en boucle. La pause au survol est gérée par SCSS.
      anchorElement.style.animationPlayState = "running";
    } else {
      // La classe 'is-scrolling-active' a déjà été retirée après la mesure.
      // this.element.classList.remove('is-scrolling-active'); // Redondant, donc supprimé

      // Réinitialiser les styles de transformation et d'animation
      anchorElement.style.transform = "translateX(0px)"; // Position par défaut
      anchorElement.style.animationName = "none"; // Pas d'animation
      anchorElement.style.animationPlayState = "paused";

      // Retirer les variables CSS pour éviter des effets de bord si elles ne sont plus nécessaires
      anchorElement.style.removeProperty("--text-width");
      anchorElement.style.removeProperty("--text-segment-width");
      anchorElement.style.removeProperty("--animation-duration");

      // Ajouter le texte simple s'il ne défile pas
      const singleSpan = document.createElement("span");
      singleSpan.textContent = this.textValue || "";
      anchorElement.appendChild(singleSpan);
    }
    // Le contentSpan est déjà dans anchorElement, pas besoin de le recréer ou de le ré-ajouter.
  }
}
