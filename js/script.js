$(document).ready(function () {
  initSelect2();
  // Fonction pour mettre à jour les index des lignes
  function updateIndex() {
    $("#devisTable tbody tr").each(function (index) {
      $(this)
        .find(".index")
        .text(index + 1);
    });
  }

  // Fonction pour ajouter une nouvelle ligne (structure révisée avec unité en select)
  $("#addRow").click(function () {
    let newRow = `
        <tr>
            <td class="index"></td>
            <td><input type="text" class="form-control" name="designation[]" placeholder="Désignation"></td>
            <td>
                <select class="form-select unite-select" name="unite[]" required>
                    ${uniteOptions}
                </select>
            </td>
            <td><input type="number" class="form-control quantite" name="quantite[]" placeholder="Quantité"></td>
            <td><input type="number" class="form-control prix" name="prix[]" placeholder="Prix Unitaire"></td>
            <td><input type="number" class="form-control total" name="total[]" placeholder="Prix Total" readonly></td>
            <td><button type="button" class="btn btn-danger remove-row">-</button></td>
        </tr>
    `;
    $("#devisTable tbody").append(newRow);
    updateIndex();
    // Initialise Select2 uniquement sur les nouveaux selects non encore initialisés
    $("#devisTable tbody tr:last .unite-select").select2({
      dropdownParent: $("#devisTable").parent(),
      width: "100%",
      placeholder: "Choisir...",
    });
  });

  // Fonction pour supprimer une ligne
  $(document).on("click", ".remove-row", function () {
    $(this).closest("tr").remove();
    calculateTotals();
    updateIndex();
  });

  // Fonction pour calculer le total par ligne (prix * quantité)
  $(document).on("input", ".prix, .quantite", function () {
    let row = $(this).closest("tr");
    let prix = parseFloat(row.find(".prix").val()) || 0;
    let quantite = parseFloat(row.find(".quantite").val()) || 0;
    let total = prix * quantite;
    row.find(".total").val(total.toFixed(2));
    calculateTotals();
  });

  // Fonction pour recalculer les totaux globaux
  function calculateTotals() {
    let totalHT = 0;
    $("#devisTable tbody tr").each(function () {
      let prix = parseFloat($(this).find(".prix").val()) || 0;
      let quantite = parseFloat($(this).find(".quantite").val()) || 0;
      let totalHTLine = prix * quantite;
      totalHT += totalHTLine;
    });
    $("#totalHT").val(totalHT.toFixed(2));
    // Si tu veux calculer la TVA sur le total, fais-le ici
    // let tva = ...;
    // let totalTTC = totalHT + tva;
    // $('#totalTTC').val(totalTTC.toFixed(2));
  }

  // Prévisualisation du logo
  $("#logoUpload").change(function () {
    let reader = new FileReader();
    reader.onload = function (e) {
      $("#logoPreview").attr("src", e.target.result).show();
      $("#logoMessage").hide(); // Cacher le message lorsque le logo est chargé
    };
    if (this.files[0]) {
      reader.readAsDataURL(this.files[0]);
    }
  });

  // Drag and Drop pour le logo
  $("#logoUploadContainer")
    .on("dragover", function (e) {
      e.preventDefault();
      e.stopPropagation();
      $(this).addClass("drag-over");
    })
    .on("dragleave", function (e) {
      e.preventDefault();
      e.stopPropagation();
      $(this).removeClass("drag-over");
    })
    .on("drop", function (e) {
      e.preventDefault();
      e.stopPropagation();
      $(this).removeClass("drag-over");
      let files = e.originalEvent.dataTransfer.files;
      if (files.length > 0) {
        $("#logoUpload").prop("files", files);
        let reader = new FileReader();
        reader.onload = function (e) {
          $("#logoPreview").attr("src", e.target.result).show();
          $("#logoMessage").hide(); // Cacher le message lorsque le logo est chargé
        };
        reader.readAsDataURL(files[0]);
      }
    });

  // Met à jour les index au chargement
  updateIndex();
});
