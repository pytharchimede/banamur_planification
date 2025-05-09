$(document).ready(function () {
  $("#exportPdfBtn").hide();

  $("#saveBtn").on("click", function (e) {
    e.preventDefault(); // Empêche le comportement de soumission du formulaire par défaut

    var form = $("#devisForm")[0];
    var formData = new FormData(form);

    // Ajoutez les valeurs des cases à cocher
    var tvaFacturable = $('input[name="tvaFacturable"]:checked').val() || "0";
    var publierDevis = $('input[name="publierDevis"]:checked').val() || "0";

    // Champs principaux
    var numeroDevis = $("#numeroDevis").val();
    var delaiLivraison = $("#delaiLivraison").val();
    var dateEmission = $("#dateEmission").val();
    var dateExpiration = $("#dateExpiration").val();
    var logoFile = $("#logoUpload")[0] ? $("#logoUpload")[0].files[0] : null;
    var clientId = $("#clientSelect").val();
    var offreId = $("#offreSelect").val();
    var termesConditions = $("#termesConditions").val() || "";
    var piedDePage = $("#piedDePage").val() || "";
    var totalHT = $("#totalHT").val() || "0";
    var totalTTC = $("#totalTTC").val() || "0";
    var tva = $("#tva").val() || "0";
    var correspondant = $("#correspondant").val() || "";

    // Récupération des lignes de devis (désignation, prix, quantité, unite_id, total)
    var lignes = [];
    $("#devisTable tbody tr").each(function () {
      var designation = $(this).find('input[name="designation[]"]').val();
      var prix = $(this).find('input[name="prix[]"]').val();
      var quantite = $(this).find('input[name="quantite[]"]').val();
      var unite_id = $(this).find('select[name="unite[]"]').val();
      var total = $(this).find('input[name="total[]"]').val();
      if (designation && prix && quantite && unite_id) {
        lignes.push({
          designation: designation,
          prix: prix,
          quantite: quantite,
          unite_id: unite_id,
          total: total,
        });
      }
    });

    // Ajoutez les valeurs au FormData
    formData.append("numeroDevis", numeroDevis);
    formData.append("delaiLivraison", delaiLivraison);
    formData.append("dateEmission", dateEmission);
    formData.append("dateExpiration", dateExpiration);
    formData.append("termesConditions", termesConditions);
    formData.append("piedDePage", piedDePage);
    formData.append("totalHT", totalHT);
    formData.append("totalTTC", totalTTC);
    formData.append("tva", tva);
    formData.append("client_id", clientId);
    formData.append("offre_id", offreId);
    formData.append("tvaFacturable", tvaFacturable);
    formData.append("publierDevis", publierDevis);
    formData.append("correspondant", correspondant);

    if (logoFile) {
      formData.append("logo", logoFile);
    }

    // Ajoute les lignes de devis sous forme de JSON
    formData.append("lignes", JSON.stringify(lignes));

    // Effectuez la requête AJAX
    $.ajax({
      type: "POST",
      url: "request/generate_devis.php",
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
