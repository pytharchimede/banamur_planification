$(document).ready(function () {
  initSelect2();

  function updateIndex() {
    let idx = 1;
    $("#devisTable tbody tr").each(function () {
      if (
        !$(this).hasClass("groupe-row") &&
        !$(this).hasClass("sous-total-row")
      ) {
        $(this).find(".index").text(idx++);
      }
    });
  }

  // Ajouter un groupe
  $("#addGroupBtn").click(function () {
    let groupRow = `
      <tr class="groupe-row">
        <td colspan="7">
          <input type="text" class="form-control groupe-titre" placeholder="Titre du groupe" required>
          <button type="button" class="btn btn-warning btn-sm close-group">Fermer le groupe</button>
          <button type="button" class="btn btn-danger btn-sm remove-group">Supprimer le groupe</button>
        </td>
      </tr>
    `;
    $("#devisTable tbody").append(groupRow);
  });

  // Ajouter une ligne dans le dernier groupe (ou hors groupe si aucun)
  $("#addRow").click(function () {
    // Cherche le dernier groupe non fermé
    let $lastGroup = $("#devisTable tbody tr.groupe-row").last();
    let groupTitle = "";
    if ($lastGroup.length && !$lastGroup.hasClass("closed")) {
      groupTitle = $lastGroup.find(".groupe-titre").val() || "";
    }
    let newRow = `
      <tr>
        <td class="index"></td>
        <td>
          <input type="text" class="form-control" name="designation[]" placeholder="Désignation">
          <input type="hidden" class="groupe-value" name="groupe[]" value="${groupTitle}">
        </td>
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
    // Ajoute la ligne juste après le dernier groupe ouvert ou à la fin
    if ($lastGroup.length && !$lastGroup.hasClass("closed")) {
      // Insère après le dernier groupe ou la dernière ligne du groupe
      let $insertAfter = $lastGroup;
      while (
        $insertAfter.next().length &&
        !$insertAfter.next().hasClass("groupe-row") &&
        !$insertAfter.next().hasClass("sous-total-row")
      ) {
        $insertAfter = $insertAfter.next();
      }
      $insertAfter.after(newRow);
    } else {
      $("#devisTable tbody").append(newRow);
    }
    updateIndex();
    $("#devisTable tbody tr:not(.groupe-row):last .unite-select").select2({
      dropdownParent: $("#devisTable").parent(),
      width: "100%",
      placeholder: "Choisir...",
    });
    calculateGroupSubtotals();
  });

  // Fermer un groupe (affiche le sous-total juste après le groupe)
  $(document).on("click", ".close-group", function () {
    let $groupRow = $(this).closest("tr");
    if ($groupRow.hasClass("closed")) return;
    $groupRow.addClass("closed");
    calculateGroupSubtotals();
  });

  // Suppression d'une ligne
  $(document).on("click", ".remove-row", function () {
    $(this).closest("tr").remove();
    calculateTotals();
    updateIndex();
    calculateGroupSubtotals();
  });

  // Suppression d'un groupe (et de ses lignes + sous-total)
  $(document).on("click", ".remove-group", function () {
    let $groupRow = $(this).closest("tr");
    let $nextRows = $groupRow.nextUntil(".groupe-row, .sous-total-row");
    $nextRows.remove();
    // Supprime le sous-total s'il existe juste après le groupe
    if ($groupRow.next().hasClass("sous-total-row")) {
      $groupRow.next().remove();
    }
    $groupRow.remove();
    updateIndex();
    calculateTotals();
    calculateGroupSubtotals();
  });

  // Met à jour le champ groupe[] de chaque ligne quand le titre du groupe change
  $(document).on("input", ".groupe-titre", function () {
    let groupTitle = $(this).val();
    let $groupRow = $(this).closest("tr");
    let $nextRows = $groupRow.nextUntil(".groupe-row, .sous-total-row");
    $nextRows.find(".groupe-value").val(groupTitle);
    calculateGroupSubtotals();
  });

  // Calcul du total par ligne
  $(document).on("input", ".prix, .quantite", function () {
    let row = $(this).closest("tr");
    let prix = parseFloat(row.find(".prix").val()) || 0;
    let quantite = parseFloat(row.find(".quantite").val()) || 0;
    let total = prix * quantite;
    row.find(".total").val(total.toFixed(2));
    calculateTotals();
    calculateGroupSubtotals();
  });

  // Calcul des sous-totaux par groupe
  function calculateGroupSubtotals() {
    // Supprime les anciennes lignes de sous-total
    $("#devisTable tbody tr.sous-total-row").remove();

    let currentGroup = null;
    let groupTotal = 0;
    let $lastRowOfGroup = null;

    $("#devisTable tbody tr").each(function () {
      if ($(this).hasClass("groupe-row")) {
        // Si on termine un groupe fermé, on insère le sous-total après la dernière ligne du groupe
        if (
          currentGroup &&
          currentGroup.hasClass("closed") &&
          $lastRowOfGroup
        ) {
          let subtotalRow = `
            <tr class="sous-total-row">
              <td colspan="5" class="text-end">Sous-total</td>
              <td><input type="number" class="form-control sous-total" value="${groupTotal.toFixed(
                2
              )}" readonly></td>
              <td></td>
            </tr>
          `;
          $lastRowOfGroup.after(subtotalRow);
        }
        currentGroup = $(this);
        groupTotal = 0;
        $lastRowOfGroup = null;
      } else if (!$(this).hasClass("sous-total-row")) {
        let total = parseFloat($(this).find(".total").val()) || 0;
        groupTotal += total;
        $lastRowOfGroup = $(this);
      }
    });

    // Ajoute une ligne de sous-total pour le dernier groupe fermé
    if (currentGroup && currentGroup.hasClass("closed") && $lastRowOfGroup) {
      let subtotalRow = `
        <tr class="sous-total-row">
          <td colspan="5" class="text-end">Sous-total</td>
          <td><input type="number" class="form-control sous-total" value="${groupTotal.toFixed(
            2
          )}" readonly></td>
          <td></td>
        </tr>
      `;
      $lastRowOfGroup.after(subtotalRow);
    }
  }

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

    // Calcul TVA si la case est cochée
    let tvaFacturable = $("#tvaFacturable").is(":checked");
    let tauxTVA = 0.18;
    let tva = tvaFacturable ? totalHT * tauxTVA : 0;
    $("#tva").val(tva.toFixed(2));

    // Total TTC
    let totalTTC = totalHT + tva;
    $("#totalTTC").val(totalTTC.toFixed(2));
  }

  // Prévisualisation du logo
  $("#logoUpload").change(function () {
    let reader = new FileReader();
    reader.onload = function (e) {
      $("#logoPreview").attr("src", e.target.result).show();
      $("#logoMessage").hide();
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
          $("#logoMessage").hide();
        };
        reader.readAsDataURL(files[0]);
      }
    });

  updateIndex();
});
