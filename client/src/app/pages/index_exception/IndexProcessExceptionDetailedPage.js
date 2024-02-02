import React, {useEffect, useMemo, useContext} from "react";
import {BrowserRouter as Router, Redirect, Route, Switch, withRouter} from 'react-router-dom';
import {makeStyles, responsiveFontSizes} from '@material-ui/core/styles';
import Button from '@material-ui/core/Button';
import Typography from '@material-ui/core/Typography';
import Container from "@material-ui/core/Container";
import Image from 'react-bootstrap/Image'
import Grid from "@material-ui/core/Grid";
import {
    Portlet,
    PortletBody,
    PortletHeader,
    PortletHeaderToolbar
} from "../../partials/content/Portlet";
import {Title} from '../home/helpers/titles'
import PermIdentityIcon from '@material-ui/icons/PermIdentity';
import clsx from 'clsx';
import TextField from '@material-ui/core/TextField';
import FormGroup from '@material-ui/core/FormGroup';
import FormControlLabel from '@material-ui/core/FormControlLabel';
import Checkbox from '@material-ui/core/Checkbox';
import Select from '@material-ui/core/Select';
import InputLabel from '@material-ui/core/InputLabel';
import FormControl from '@material-ui/core/FormControl';
import moment from 'moment';
import Downshift from "downshift";
import Autocomplete from "../entity/TestAutocomplete";
import CustomFileInput from "reactstrap/es/CustomFileInput";
import CircularProgress from '@material-ui/core/CircularProgress';
import {withAuth} from '@okta/okta-react';

import PropTypes from 'prop-types';
import ErrorIcon from '@material-ui/icons/Error';
import InfoIcon from '@material-ui/icons/Info';
import CloseIcon from '@material-ui/icons/Close';
import {amber, green} from '@material-ui/core/colors';
import IconButton from '@material-ui/core/IconButton';
import Snackbar from '@material-ui/core/Snackbar';
import SnackbarContent from '@material-ui/core/SnackbarContent';
import WarningIcon from '@material-ui/icons/Warning';
import CheckCircleIcon from '@material-ui/icons/CheckCircle';
import {indexExceptionDetail,reIndexFile} from "../../crud/index-process-exception.crud";

const useStylesFacebook = makeStyles({
    root: {
        position: 'relative',
    },
    top: {
        color: '#eef3fd',
    },
    bottom: {
        color: '#6798e5',
        animationDuration: '550ms',
        position: 'absolute',
        left: 0,
    },
});

const useStyles = makeStyles(theme => ({
    root: {
        flexGrow: 1,
    },
    backButton: {
        marginRight: theme.spacing(1),
    },
    instructions: {
        marginTop: theme.spacing(1),
        marginBottom: theme.spacing(1),
    },
    adjustment: {
        marginRight: '5px',
    },
    companyinfo: {
        listStyle: 'none',
        padding: '0px',
        minHeight: '100px'

    },
    listItem: {
        marginBottom: '5px'
    },
    textField: {
        // marginLeft: theme.spacing(1),
        // marginRight: theme.spacing(1),
        width: '100%',
    },

    textFieldOther: {
        width: '100%',
    },

    textFieldCity: {

        marginTop: 3,
    },


    textFieldtwofield: {
        // marginLeft: theme.spacing(1),
        // marginRight: theme.spacing(1),
        width: '100%',
    },


    selectField: {
        // marginLeft: theme.spacing(1),
        // marginRight: theme.spacing(1),
        width: '100%',
        marginTop: 16,
    },
    dense: {
        marginTop: 16,
    },
    container: {
        display: 'flex',
        flexWrap: 'wrap',
    },
    checkbox: {
        marginTop: 30
    },

    fileUploading: {
        zIndex: 0,
        marginTop: 22,
    },

    submitButton: {
        marginTop: 15,
        float: 'right',
        display: 'inline-flex'
    },

    loader: {
        marginTop: 7,
    },
    success: {
        backgroundColor: green[600],
    },
    error: {
        backgroundColor: theme.palette.error.dark,
    },
    info: {
        backgroundColor: theme.palette.primary.main,
    },
    warning: {
        backgroundColor: amber[700],
    },
    icon: {
        fontSize: 20,
    },
    iconVariant: {
        opacity: 0.9,
        marginRight: theme.spacing(1),
    },
    message: {
        display: 'flex',
        alignItems: 'center',
    },


}));


const variantIcon = {
    success: CheckCircleIcon,
    warning: WarningIcon,
    error: ErrorIcon,
    info: InfoIcon,
};


function MySnackbarContentWrapper(props) {
    const classes = useStyles();
    const {className, message, onClose, variant, ...other} = props;
    const Icon = variantIcon[variant];

    return (
        <SnackbarContent

            elevation={6} variant="filled"
            className={clsx(classes[variant], className)}
            aria-describedby="client-snackbar"
            message={
                <span id="client-snackbar" className={classes.message}>
          <Icon className={clsx(classes.icon, classes.iconVariant)}/>
                    {message}
        </span>
            }
            action={[
                <IconButton key="close" aria-label="Close" color="inherit" onClick={onClose}>
                    <CloseIcon className={classes.icon}/>
                </IconButton>,
            ]}
            {...other}
        />
    );
}

MySnackbarContentWrapper.propTypes = {
    className: PropTypes.string,
    message: PropTypes.node,
    onClose: PropTypes.func,
    variant: PropTypes.oneOf(['success', 'warning', 'error', 'info']).isRequired,
}

const IndexProcessExceptionDetailPage = (props) => {


    const classes = useStyles();
    const inputLabel = React.useRef(null);
    const [labelWidth, setLabelWidth] = React.useState(0);
    const [addressObject, setAddressObject] = React.useState([]);
    const [addressValue, setAddressValue] = React.useState('');
    const [loading, setLoading] = React.useState(false)
    const [error, setError] = React.useState(false)
    const [success, setSuccess] = React.useState(false)
    //form state

    const [entityName, setEntityName] = React.useState({value: '', error: ' ',});
    const [exceptionID, setExceptionID] = React.useState('');
    const [exceptionImgUrl, setExceptionImgUrl] = React.useState('');
    useEffect(()=>{
        fetchIndexExceptionDetailed();
    },[])


   const fetchIndexExceptionDetailed = async () => {
      const detailedView = await indexExceptionDetail(props.match.params.id);
      console.log(detailedView);
      setExceptionImgUrl(detailedView.data.object_name)
      setExceptionID(detailedView.data.id)
      setEntityName({...entityName, value: detailedView.data.fetched_entity_name})
       setLoading(false);
   }

   const saveReindexFile = async () => {
        const response = await reIndexFile(exceptionID,entityName.value)
        //console.log(response.status)
        if(response.error){
            setEntityName({...entityName, error: response.message.name[0]})
            setError(true)
        }
        else{
            setError(false)
            setSuccess(true)
           
        }
        
        setLoading(false);
        
   }
    function FacebookProgress(props) {
        const classes = useStylesFacebook();

        return (
            <div className={classes.root}>
                <CircularProgress

                    variant="determinate"
                    value={100}
                    className={classes.top}
                    size={24}
                    thickness={4}
                    {...props}
                />
                <CircularProgress
                    variant="indeterminate"
                    disableShrink
                    className={classes.bottom}
                    size={24}
                    thickness={4}
                    {...props}
                />
            </div>
        );
    }


    const handleClose = (event, reason) => {
        setError(false);
    }
    const handleSuccessClose = (event, reason) => {
        setSuccess(false);
    }

    const handleOnSubmit = (event) => {
        event.preventDefault();
        
        saveReindexFile()
        
        setLoading(true);
        console.log('Info', `Welcome ${entityName.value}`);
    }

    return (

        <div className={classes.root}>
            <Grid container spacing={1}>
                <Grid item xs={12} sm={4}>
                    <Portlet>
                        <PortletHeader icon={<PermIdentityIcon className={classes.adjustment}/>}
                                       title="Mannual File Indexing"/>
                        <PortletBody>
                            {error ? (<MySnackbarContentWrapper
                                onClose={handleClose}
                                variant="error"
                                message="Something went wrong"
                            />) : ''}
                            
                            {success ? (<MySnackbarContentWrapper
                                onClose={handleSuccessClose}
                                variant="success"
                                message="File Successfully re-indexed!"
                            />) : ''}

                            <div className="row">
                                <form className={classes.container} onSubmit={handleOnSubmit} noValidate
                                    autoComplete="off">
                                    <FormGroup row>
                                        <div className={'col-md-12'}>
                                            <TextField
                                                disabled={loading}
                                                required
                                                error={entityName.error !== ' '}
                                                onChange={e => setEntityName({...entityName, value: e.target.value})}
                                                id="entity_name"
                                                label="Entity Name"
                                                className={clsx(classes.textField, classes.dense)}
                                                margin="dense"
                                                helperText={entityName.error}
                                                value={entityName.value}
                                            />
                                        </div>
                                        <div className={' offset-xs-10 col-xs-2'}>
                                            <input className={clsx('btn btn-primary float-right', classes.restButton)}
                                                    type="submit" value="Index File"/>
                                        </div>
                                        
                                    </FormGroup>
                                </form>
                            </div>
                        </PortletBody>
                    </Portlet>
                </Grid>
                <Grid item xs={12} sm={8}>
                    <div className={'col-xs-12'}>
                        <Image src={exceptionImgUrl} fluid />                               
                    </div>
                </Grid>
            </Grid>
        </div>
    )
}


export default withAuth(IndexProcessExceptionDetailPage);
