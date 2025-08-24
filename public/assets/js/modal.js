function showModal( id ) {
    var modal = document.getElementById( id );
    modal.style.display = "block";
}

window.onclick = function (event) {
    if( event.target.classList.contains('modal-app') ){
        hideModal(event.target.id);
    }
}

function hideModal( id ) {
    var modal = document.getElementById( id );
    modal.style.display = "none";
}
