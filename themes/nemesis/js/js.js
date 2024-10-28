$().ready(function() {


	
});

cot.getPeriodicalRequest().add(
    'some-request',
    {bar: 'baz'},
    (result) => { console.log(result) },
    4
);