$(document).ready(function () {
  $("#exportPdfBtn").hide();

  // Calcul automatique des totaux à chaque modification
  function calculerTotaux() {
    let totalHT = 0;
    $("#devisTable tbody tr").each(function () {
      let quantite =
        parseFloat($(this).find('input[name="quantite[]"]').val()) || 0;
      let prixUnitaire =
        parseFloat($(this).find('input[name="prix_unitaire[]"]').val()) || 0;
      let prixTotal = quantite * prixUnitaire;
      $(this).find('input[name="prix_total[]"]').val(prixTotal.toFixed(2));
      totalHT += prixTotal;
    });
    $("#totalHT").val(totalHT.toFixed(2));
    let tva = 0;
    if ($("#tvaFacturable").is(":checked")) {
      tva = totalHT * 0.18;
    }
    $("#tva").val(tva.toFixed(2));
    $("#totalTTC").val((totalHT + tva).toFixed(2));
  }

  // Déclenche le calcul lors de la saisie
  $(document).on(
    "input",
    'input[name="quantite[]"], input[name="prix_unitaire[]"]',
    function () {
      calculerTotaux();
    }
  );

  // Déclenche le calcul lors du changement de la TVA
  $("#tvaFacturable").on("change", function () {
    calculerTotaux();
  });

  // Calcul initial au chargement
  calculerTotaux();

  $("#saveBtn").on("click", function (e) {
    e.preventDefault();

    var form = $("#devisForm")[0];
    var formData = new FormData(form);

    // Ajoutez les valeurs des cases à cocher
    var tvaFacturable = $("#tvaFacturable").is(":checked") ? "1" : "0";
    var publierDevis = $("#publierDevis").is(":checked") ? "1" : "0";

    // Champs principaux
    var logoFile = $("#logoUpload")[0] ? $("#logoUpload")[0].files[0] : null;

    // Récupération des lignes de devis (désignation, prix_unitaire, quantité, unite_id, prix_total, groupe)
    var lignes = [];
    $("#devisTable tbody tr").each(function () {
      var designation = $(this).find('input[name="designation[]"]').val();
      var prix_unitaire = $(this).find('input[name="prix_unitaire[]"]').val();
      var quantite = $(this).find('input[name="quantite[]"]').val();
      var unite_id = $(this).find('select[name="unite[]"]').val();
      var prix_total = $(this).find('input[name="prix_total[]"]').val();
      var groupe = $(this).find('input[name="groupe[]"]').val() || "";
      if (designation && prix_unitaire && quantite && unite_id) {
        lignes.push({
          designation: designation,
          prix_unitaire: prix_unitaire,
          quantite: quantite,
          unite_id: unite_id,
          prix_total: prix_total,
          groupe: groupe,
        });
      }
    });

    // Ajoute les lignes de devis sous forme de JSON
    formData.set("lignes", JSON.stringify(lignes));
    formData.set("tvaFacturable", tvaFacturable);
    formData.set("publierDevis", publierDevis);

    if (logoFile) {
      formData.set("logo", logoFile);
    }

    // Effectuez la requête AJAX
    $.ajax({
      type: "POST",
      url: "request/update_devis.php",
      data: formData,
      dataType: "text",
      cache: false,
      contentType: false,
      processData: false,
      success: function (response) {
        console.log("Réponse du serveur:", response);
        $("#exportPdfBtn").show();
        $("#saveBtn").hide();
      },
      error: function (xhr, status, error) {
        console.error("Erreur:", status, error);
      },
    });
  });

  $("#exportPdfBtn").on("click", function () {
    $(location).attr("href", "request/export_pdf.php");
  });
});
