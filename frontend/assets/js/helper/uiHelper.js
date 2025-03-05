export function getActionbuttons(editID, editTarget, deleteID, id,extraId="") {
  return `<button id="${editID}" data-id="${id}" ${extraId} class="edit-btn btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#${editTarget}"><i class="fa-solid fa-pen-to-square"></i></button>
  <button id="${deleteID}" data-id="${id}" class="delete-btn btn btn-danger btn-sm"><i class="fa-solid fa-trash"></i></button>`;
}

export function getBootStrapModal(ModalId) {
    return ModalId ? bootstrap.Modal.getInstance(
        document.getElementById(ModalId)
    ) : "";
}

export function SwalPopup (Swal,message,icons) {
    Swal.fire({
        title: "New Mesage",
        text: message,
        icon: icons,
        timer: 5000
    })
}

export function deleteSwalPopup(Swal) {
    return Swal.fire({
        title: "Are you sure?",
        text: "You won't be able to revert this!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes, delete it!",
      })
}

export function getElementValue(element) {
    return (element ? $("#"+element).val(): "");
}
