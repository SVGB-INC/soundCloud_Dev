if ($('#selectGenre').length > 0) {
    $('#selectGenre').select2({
        tags: false,
        maximumSelectionLength: 5,
    });
}

if ($('#tags').length > 0) {
    $('#tags').select2({
        tags: true,
        maximumSelectionLength: 5,
    });
}

// $('#show').on('click', function (e) {
//     alert($('#tags').val());
// });

// $('#show').on('click', function (e) {
//     alert($('#selectGenre').val());
// });