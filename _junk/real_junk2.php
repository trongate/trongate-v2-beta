

input, textarea {
    margin-bottom: 2.4rem;
}

input[type=text], #select-box {
  width: 84vw;
  clear: both;
  color: #000;
  font-family: 'c64_userregular';
  background-color: #fff;
  border: none;
  padding: 1.4rem 1rem;
  text-transform: initial;
  font-size: 1.8rem;
}

#select-box {
    padding-bottom: 1rem;
    overflow: hidden;
    margin: 0 auto;
    width: 82vw;
    text-transform: uppercase;
}

input[type=button], input[type=submit], input[type=reset], button {
  width: 84vw;
  clear: both;
  color: #eee;
  font-family: 'c64_userregular';
  background-color: #9898ee;
  border: none;
  cursor: pointer;
  padding: 1.5rem 1rem;
}

a:link { color: #A0A0FF; }
a:active { color: #A0A0FF; }
a:visted { color: #A0A0FF; }
a:hover { color: #FFF; cursor: pinter; }

#close a { margin: 0 1rem; background-color: #4040E0; }

#stage, #close {
    z-index: 9;
}

#loader-container {
    top: 160px;
    position: fixed;
    width: 100%;
}

#thy-loader {
    margin: 0 auto;
    position: relative;
    -webkit-animation: fadein 1s; /* Safari, Chrome and Opera > 12.1 */
}

@keyframes fadein {
    from { opacity: 0; }
    to   { opacity: 1; }
}

/* Safari, Chrome and Opera > 12.1 */
@-webkit-keyframes fadein {
    from { opacity: 0; }
    to   { opacity: 1; }
}

ul {
    left: 1rem;
    position: relative;
    margin-top: 3rem;
    text-align: left;
    display: inline-block;
}

li {
    list-style: none;
    margin-bottom: 1.6em;
}

.blink_me {
  animation: blinker 1s linear infinite;
  color: white;
}

.delete-btn {
    background: red!important;
    color: white !important;
}

.delete-btn:hover {
    background: black!important;
    color: red !important;
}

.button-gold {
    background: #eeee98 !important;
    color: #4040E0 !important;
}

.button-green {
    background: #98eec3 !important;
    color: #4040E0 !important;
}

.button-pink {
    background: #fad7fa !important;
    color: #4040E0 !important;
}

#centroid {
    display: flex;
    align-items: center;
    justify-content: center;
    align-self: center;
}

#possible_options, #possible_options_mini {
    left: 0 !important;
    background: white;
    width: 84vw;
    margin: 3rem 0 0 0;
    padding: 0;
    overflow: auto;
    max-height: 60vh;
}

#option:hover, #option.active  {
    background: black;
    color: white;
    cursor: pointer;
}

#option {
    border: 1px black solid;
    height: 2rem;
    padding: 1.4rem;
    margin: 0;
}

textarea, .code-preview {
    width: 84vw;
    clear: both;
    display: block;
    height: 20rem;
    margin-left: auto;
    margin-right: auto;
    padding: 1rem;
    font-family: "Courier New", Courier, monospace;
    font-weight: bold;
    font-size: 2.1rem;
    line-height: 2.7rem;
}

.error {
    background: white;
    color: red;
    line-height: 3.2rem;
    text-align: left;
    padding: 1rem;
    width: 81vw;
    clear:both;
    margin-left: auto;
    margin-right: auto;
}

.text-center {
    text-align: center;
}

.center-text {
    text-align: center;
}

.code-preview {
    overflow: auto;
    font-size: 0.9em;
    line-height: 1.2em;
    text-transform: none;
    color: #eee;
    border: 1px #eee dashed;
    height: 15.4rem;
}

.triple-btn {
    display: flex;
    flex-direction: row;
    align-items: center;
    justify-content: space-around;
}

.triple-btn button {
    width: 100%;
}

@keyframes blinker {
  50% {
    opacity: 0;
  }
}